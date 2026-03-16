<?php

namespace App\Http\Controllers;

use App\Services\FocusFlowBoardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected FocusFlowBoardService $board
    ) {}

    public function index(Request $request): View
    {
        return view('focusflow.index', $this->board->buildSmartView($request->user(), 'all'));
    }

    public function smartView(Request $request, string $view): View
    {
        abort_unless($this->board->hasSmartView($view), 404);

        return view('focusflow.index', $this->board->buildSmartView($request->user(), $view));
    }
}
