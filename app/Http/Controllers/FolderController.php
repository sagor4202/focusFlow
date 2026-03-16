<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\UpdateFolderRequest;
use App\Models\Folder;
use App\Services\FocusFlowBoardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FolderController extends Controller
{
    public function __construct(
        protected FocusFlowBoardService $board
    ) {}

    public function store(StoreFolderRequest $request): RedirectResponse
    {
        $folder = $request->user()->folders()->create($request->validated());

        return redirect()
            ->route('folders.show', $folder)
            ->with('status', 'Folder created successfully.');
    }

    public function show(Request $request, Folder $folder): View
    {
        $this->authorize('view', $folder);

        return view('focusflow.index', $this->board->buildFolder($request->user(), $folder));
    }

    public function update(UpdateFolderRequest $request, Folder $folder): RedirectResponse
    {
        $folder->update($request->validated());

        return back()->with('status', 'Folder renamed successfully.');
    }

    public function destroy(Folder $folder): RedirectResponse
    {
        $this->authorize('delete', $folder);

        $folderName = $folder->name;
        $folder->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', sprintf('"%s" was deleted.', $folderName));
    }
}
