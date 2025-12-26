@extends('layouts.app')

@section('title', $lesson->title . ' • ' . $course->title)

@section('content')
    <div class="row" style="justify-content: space-between; margin-bottom: 16px;">
        <div>
            <a href="{{ route('courses.show', $course->slug) }}" style="color: white;">← Back to Course</a>
        </div>
        <div class="pill">{{ $course->title }}</div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <h1 style="margin:0 0 8px; font-size:24px;">{{ $lesson->position }}. {{ $lesson->title }}</h1>
        @if($lesson->description)
            <p class="muted" style="margin-top:8px;">{{ $lesson->description }}</p>
        @endif

        @if(!empty($lesson->content_url))
            @php($url = $lesson->content_url)
            <div style="margin-top:12px;">
                @if(Str::contains(Str::lower($url), 'youtube.com') || Str::contains(Str::lower($url), 'youtu.be'))
                    @php($embed = Str::contains($url, 'watch?v=') ? 'https://www.youtube.com/embed/' . Str::after($url, 'watch?v=') : 'https://www.youtube.com/embed/' . basename($url))
                    <iframe width="100%" height="420" src="{{ $embed }}" title="YouTube video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                @elseif(Str::endsWith(Str::lower($url), '.mp4'))
                    <video controls style="width:100%; border-radius:8px;">
                        <source src="{{ $url }}" type="video/mp4" />
                        Your browser does not support the video tag.
                    </video>
                @else
                    <a class="badge" href="{{ $url }}" target="_blank">Open content</a>
                @endif
            </div>
        @endif

        @if($lesson->material_file)
            <div style="margin-top:12px;">
                <a href="{{ asset('storage/' . $lesson->material_file) }}"
                target="_blank"
                class="badge">
                    📄 View Lesson Material/Note
                </a>
            </div>
        @endif

        @if($isCompleted)
            <div class="badge" style="margin-top:12px;color:#10b981;border-color:#10b98144;">✔ Completed</div>
        @endif

        <div class="row" style="margin-top:12px;">
            @if($prevLesson)
                <a class="badge" href="{{ route('lessons.show', [$course->slug, $prevLesson->slug]) }}">← Prev</a>
            @else
                <span class="badge muted">← Prev</span>
            @endif

            @if($nextLesson)
                <a class="badge" href="{{ route('lessons.show', [$course->slug, $nextLesson->slug]) }}">Next →</a>
            @else
                <span class="badge muted">Next →</span>
            @endif
        </div>
    </div>

    @if(!empty($lesson->quiz_json) && !$isCompleted)
    <div class="card" style="margin-bottom:16px;">
        <div class="section-title">Quiz</div>
        @if($quizAttempt)
            <div class="muted" style="margin-bottom:8px;">
                Previous attempt score: {{ $quizAttempt->score }} / {{ $quizAttempt->total }}
            </div>
        @endif
        <div id="quiz-container"></div>
        <div id="quiz-result" class="muted" style="margin-top:8px;">
            @if($quizAttempt)
                Previous score: {{ $quizAttempt->score }} / {{ $quizAttempt->total }}
            @endif
        </div>
        <div class="row" style="gap:8px; margin-top:12px;">
            <button id="submit-quiz" class="pill">Submit Quiz</button>
        </div>
        <script type="application/json" id="quiz-data">{!! $lesson->quiz_json !!}</script>
        <script>
            (function(){
                const root = document.getElementById('quiz-container');
                let data;
                try {
                    const raw = document.getElementById('quiz-data').textContent.trim();
                    data = JSON.parse(raw);
                } catch(e) {
                    root.innerHTML = '<div class="muted">Invalid quiz format.</div>';
                    return;
                }
                if(!data || !Array.isArray(data.questions) || data.questions.length === 0){
                    root.innerHTML = '<div class="muted">No questions provided.</div>';
                    return;
                }
                // Render questions
                data.questions.forEach((q, qi) => {
                    const fs = document.createElement('fieldset');
                    fs.style.marginTop = '8px';
                    const legend = document.createElement('div');
                    legend.textContent = (qi+1) + '. ' + (q.text || '');
                    legend.style.fontWeight = '600';
                    fs.appendChild(legend);
                    if(Array.isArray(q.options)){
                        q.options.forEach((opt, oi) => {
                            const label = document.createElement('label');
                            label.style.display = 'block';
                            label.style.margin = '6px 0';
                            const input = document.createElement('input');
                            input.type = 'radio';
                            input.name = 'q_' + qi;
                            input.value = oi;
                            input.style.marginRight = '8px';
                            label.appendChild(input);
                            label.appendChild(document.createTextNode(String(opt)));
                            fs.appendChild(label);
                        });
                    }
                    root.appendChild(fs);
                });

                const submitBtn = document.getElementById('submit-quiz');
                const resultEl = document.getElementById('quiz-result');
                submitBtn.addEventListener('click', function () {
                    let correct = 0;

                    data.questions.forEach((q, qi) => {
                        const selected = document.querySelector('input[name="q_' + qi + '"]:checked');
                        const answerIndex = Number(q.answer);
                        if (selected && Number(selected.value) === Number(q.answer)) {
                            correct++;
                        }
                    });

                    const total = data.questions.length;
                    resultEl.textContent = 'Score: ' + correct + ' / ' + total;

                    fetch("{{ route('lessons.quiz.attempt', [$course->slug, $lesson->slug]) }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            score: correct,
                            total: total
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.completed) {
                            resultEl.textContent += ' — Great job! Lesson completed.';
                            setTimeout(() => location.reload(), 1500);
                        }
                    });
                });
            })();
        </script>
    </div>
    @endif

    <div class="card">
        <div class="section-title">All Lessons</div>
        <div class="grid">
            @foreach($lessons as $l)
                <a class="badge" href="{{ route('lessons.show', [$course->slug, $l->slug]) }}">{{ $l->position }}. {{ $l->title }}</a>
            @endforeach
        </div>
    </div>
@endsection