<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MemberItem;
use Illuminate\Http\Request;

class MemberItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Category::with('menus')->get();
        $memberItem = MemberItem::with('menu')->get();

        return view('admin.menu_member.index')->with([
            'menus' => $menus,
            'memberItem' => $memberItem
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|unique:member_items',
            'point' => 'required|numeric'
        ]);

        MemberItem::create($request->all());

        return back()->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $menus = Category::with('menus')->get();
        $memberItem = MemberItem::findOrFail($id);

        return view('admin.menu_member.edit', compact('menus', 'memberItem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $memberItem = MemberItem::findOrFail($id);

        $request->validate([
            'menu_id' => 'required|unique:member_items,menu_id,' . $memberItem->id,
            'point' => 'required|numeric'
        ]);

        $memberItem->update($request->all());

        return redirect()->route('admin.menu-member.index')->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $memberItem = MemberItem::findOrFail($id);
        $memberItem->delete();

        return back()->with('success', 'Menu deleted successfully.');
    }
}
