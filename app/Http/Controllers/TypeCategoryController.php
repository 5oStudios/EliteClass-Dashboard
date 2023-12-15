<?php

namespace App\Http\Controllers;

use DB;
use App\BBL;
use App\Course;
use App\Categories;
use App\SubCategory;
use App\ChildCategory;
use App\OfflineSession;
use App\secondaryCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class TypeCategoryController extends Controller {

    public function __construct() {
        return $this->middleware('auth');
    }


    public function index(Request $request) {
        abort_if(!auth()->user()->can('subcategories.view'), 403, 'User does not have the right permissions.');
        
        $categories = Categories::where('status', 1)->latest('id')->get();
        $typeCategories = secondaryCategory::latest('id');

        if ($request->ajax()) {
            return DataTables::of($typeCategories)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {

                    $chk = "<div class='inline'>
                              <input type='checkbox' form='bulk_delete_form' class='filled-in material-checkbox-input' name='checked[]'' value='$row->id' id='checkbox$row->id'>
                              <label for='checkbox$row->id' class='material-checkbox'></label>
                            </div>";

                    return $chk;
                })

                ->editColumn('image', 'admin.category.type.datatables.image')
                ->editColumn('title', function ($row) {

                    return $row->title ?? '';
                })
                ->editColumn('slug', function ($row) {

                    return $row->slug ?? '';
                })
                ->editColumn('status', function ($row) {
                    
                    return "<span class='btn btn-rounded " . ($row->status == 1 ? 'btn-success-rgba' : 'btn-danger-rgba') . "'>
                            " . ($row->status ? __('adminstaticword.Active') : __('adminstaticword.Deactive')) . "
                        </span>";
                })
                ->editColumn('action', 'admin.category.type.datatables.action')
                ->rawColumns(['checkbox', 'image', 'title', 'slug', 'status', 'action'])
                ->make(true);
        }

        return view('admin.category.type.index', compact('categories'));
    }


    public function create() {
        abort_if(!auth()->user()->can('subcategories.create'), 403, 'User does not have the right permissions.');
        $category = Categories::where('status', 1)->get();
        $scnd_category = secondaryCategory::where('status', 1)->get();
        return view('admin.category.type.insert', compact('scnd_category', 'category'));
    }


    public function store(Request $request) {

        abort_if(!auth()->user()->can('subcategories.create'), 403, 'User does not have the right permissions.');

        $request->validate([
            "category_id" => "required",
            "title" => "required",
            'image' => 'required|mimes:jpg,jpeg,png|max:10240',
        ],[
            "category_id.required" => __('Country name is required'),
            "title.required" => __('Type of institute name is required'),
            'image.required' => __('Image is required'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
        ]);

        $input = $request->all();

        if ($file = $request->file('image')) {

            $path = 'images/typecategory/';

            if(!file_exists(public_path().'/'.$path)) {
              
              $path = 'images/typecategory/';
              File::makeDirectory(public_path().'/'.$path,0777,true);
            }   

            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/typecategory/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
  
            $input['image'] = $image;
        }

        if (app()->getLocale() == 'en') {
            $slug = str_slug($request->title, '-');
            $input['slug'] = $slug;
        }

        $input['status'] = isset($request->status) ? 1 : 0;
        secondaryCategory::create($input);

        // Session::flash('success', trans('flash.AddedSuccessfully'));
        return redirect('typecategory')->with('success', trans('flash.AddedSuccessfully'));
    }


    public function show($id) {
        abort(404);
    }


    public function edit($id) {
        abort_if(!auth()->user()->can('subcategories.edit'), 403, 'User does not have the right permissions.');

        $cate = secondaryCategory::findOrFail($id);
        $categories = Categories::where('status',1)->get();

        return view('admin.category.type.edit', compact('categories', 'cate'));
    }


    public function update(Request $request, $id) {
        abort_if(!auth()->user()->can('subcategories.edit'), 403, 'User does not have the right permissions.');

        $request->validate([
            "category_id" => "required",
            "title"=>"required|unique:secondary_categories,title,".$id,
            'image' => 'mimes:jpg,jpeg,png|max:10240',
        ],[
            "category_id.required" => __('Country name is required'),
            "title.required" => __('Type of institute name is required'),
            'image.mimes' => __('Image must be a type of jpeg, jpg or png'),
            'image.max' => __('Image size should not be more than 10 MB'),
        ]);

        $data = secondaryCategory::findorfail($id);
        $input = $request->all();

        if ($file = $request->file('image')) {

            $path = 'images/typecategory/';

            if(!file_exists(public_path().'/'.$path)) {
              
              $path = 'images/typecategory/';
              File::makeDirectory(public_path().'/'.$path,0777,true);
            }   

            if ($data->image != null) {
                $content = @file_get_contents(public_path() . '/images/typecategory/' . $data->image);
                if ($content) {
                    unlink(public_path() . '/images/typecategory/' . $data->image);
                }
            }
  
            $optimizeImage = Image::make($file);
            $optimizePath = public_path() . '/images/typecategory/';
            $image = time() . $file->getClientOriginalName();
            $optimizeImage->save($optimizePath . $image, 72);
  
            $input['image'] = $image;
        }

        $input['status'] = isset($request->status) ? 1 : 0;

        if (app()->getLocale() == 'en') {
            $slug = str_slug($request->title, '-');
            $input['slug'] = $slug;
        }

        $data->update($input);

        // Session::flash('success', trans('flash.UpdatedSuccessfully'));
        return redirect('typecategory')->with('success', trans('flash.UpdatedSuccessfully'));
    }

    
    public function destroy($id) {
        abort_if(!auth()->user()->can('subcategories.delete'), 403, 'User does not have the right permissions.');
        
        $category = secondaryCategory::findorfail($id);
        $course = Course::where('scnd_category_id', $id)->exists();
        $meeting = BBL::where('scnd_category_id', $id)->exists();
        $session = OfflineSession::where('scnd_category_id', $id)->exists();

        if ($course) {
            return back()->with('delete', trans('flash.CannotDeleteCategory'));
        } else if($meeting || $session) {
            return back()->with('delete', trans('flash.CannotDeleteCate'));
        } else {
            if (File::exists('images/typecategory/'.$category->image)) {
                File::delete('images/typecategory/'.$category->image);
                $category->delete();
                SubCategory::where('scnd_category_id', $id)->delete();
                ChildCategory::where('scnd_category_id', $id)->delete();
            } else {
                $category->delete();
                SubCategory::where('scnd_category_id', $id)->delete();
                ChildCategory::where('scnd_category_id', $id)->delete();
            }

            return back()->with('delete', trans('flash.DeletedSuccessfully'));
        }

        return redirect('typecategory');
    }


    public function status(Request $request) {
        abort_if(!auth()->user()->can('subcategories.edit'), 403, 'User does not have the right permissions.');

        $cat = secondaryCategory::find($request->id);
        $cat->status = $request->status;
        $cat->save();
        return back()->with('success', trans('flash.UpdatedSuccessfully'));
    }
    

    public function bulk_delete(Request $request) {
        abort_if(!auth()->user()->can('subcategories.delete'), 403, 'User does not have the right permissions.');
        $validator = Validator::make($request->all(), ['checked' => 'required']);

        if ($validator->fails()) {
            return back()->with('error', trans('Please select field to be deleted.'));
        
        }else{
            foreach($request->checked as $id){
                $this->destroy($id);
            }
            // secondaryCategory::whereIn('id', $request->checked)->delete();
            // SubCategory::whereIn('scnd_category_id', $request->checked)->delete();
            // ChildCategory::whereIn('scnd_category_id', $request->checked)->delete();
        }

        // return back()->with('error', trans('Selected TypeCategory has been deleted.'));
        return back();
    }

}