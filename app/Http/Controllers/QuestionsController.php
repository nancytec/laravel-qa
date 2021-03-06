<?php

namespace App\Http\Controllers;

use App\Http\Requests\AskQuestionRequest;
use App\Models\Question;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $questions = Question::with('user')->latest()->paginate(10);
        return view('questions.index', compact('questions'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $question = new Question();
        return view('questions.create', compact('question'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AskQuestionRequest $request)
    {
        $request->user()->questions()->create($request->only('title', 'body'));

        session()->flash('success', 'Your question has been submitted');
        return redirect()->route('questions.index');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Contrxacts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Question $question)
    {
        //increment the number of views
        $question->increment('views');

        return view('questions.show', compact('question'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Question $question)
    {
        if (\Gate::denies('update-question', $question)){
            abort(403, "Access denied"); //if not permitted
        }
        return view('questions.edit', compact('question'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AskQuestionRequest $request, Question $question)
    {
        if (\Gate::denies('update-question', $question)){
            abort(403, "Access denied"); //if not permitted
        }
        $question->update($request->only('title', 'body'));


        if ($request->expectsJson()){ //This is coming from a form submit
            return response()->json([
                'message' => 'Your question has been updated',
                'body_html' => $question->body_html
            ]);
        }

        session()->flash('success', 'Your question has been updated');
        return redirect()->route('questions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Question $question)
    {
        if (\Gate::denies('delete-question', $question)){
            abort(403, "Access denied"); //if not permitted
        }
        $question->delete();

        if (request()->expectsJson())
        { //Notice this is not coming from a form submit
            return response()->json([
                'message' => 'Your question has been deleted',
            ]);
        }
        session()->flash('success', 'Your question has been deleted');
        return redirect()->route('questions.index');

    }
}
