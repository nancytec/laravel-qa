<div class="media post">

    @include('shared._vote', [
        'model' => $answer
    ])

    <div class="media-body">
        {!! $answer->body_html !!}
        <div class="row">
            <div class="col-4">
                <div class="ml-auto">
                    @can('update', $answer)
                        <a href="{{route('questions.answers.edit', [$question->id, $answer->id])}}" class="btn btn-sm btn-outline-info">Edit</a>
                    @endif
                    @can('delete', $answer)
                        <form class="form-delete" method="post" action="{{route('questions.answers.destroy', [$question->id, $answer->id])}}">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure')">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
                @include('shared._authored', [
                    'model' => $answer,
                    'label' => 'answered'
                        ])
            </div>
        </div>

    </div>
</div>