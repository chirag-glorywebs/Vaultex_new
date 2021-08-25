<?php
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
use App\Models\Reviews;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule;
use Session;
use Yajra\DataTables\Facades\DataTables;


class ProductReviewController extends Controller
{

    /**
     * Get all Reivews
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $page_title = 'Product Feedback';
        $page_description = 'showing list of feedsbase which added by users for specific product.'; 
        $user_id = Auth::user()->id;
        if ($request->ajax()) {
            
            $query = DB::table('reviews')
                    ->join('products','reviews.proid','=','products.id')
                    ->join('users','reviews.userid','=','users.id')
                     ->select('reviews.*','products.product_name','users.name','users.first_name') 
                    ->where('users.salesperson',$user_id);
                     
            $reviews =  $query->orderby('reviews.id','DESC')->get();
      
            return Datatables::of($reviews)
                ->addIndexColumn()
                ->addColumn('action', function ($review) {
                    return view('admin.feedback.feedbacks_datatable', compact('review'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }
       return view("admin.feedback.index", compact('page_title', 'page_description'));
         
    } 

     /**
     * Delete Review
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pageDelete = Reviews::findOrFail($id);
        $pageDelete->delete();
        return redirect()->route('feedbacks.index')->with('success', 'Deleted successfully !!');
    }


    /**
     * Create Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',  
            'comment' => 'required',  
            'proid' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }else{
            $user_id = Auth::user()->id;
            $review = new Reviews;
            $review->title = $request->title;
            $review->comment = $request->comment;
            $review->rating = $request->rating;
            $review->proid = $request->proid;
            $review->userid = $user_id; 
            $restult = $review->save();
            return $this->sendResponse($restult, 'Your review has been post successfully');
        }
    
    }

    /**
     * Get Sigle Review api
     *
     * @return \Illuminate\Http\Response
     */
    public function reviews($id)
    {   
        $restult =  Reviews::where('id',$id)->get();
        return $this->sendResponse($restult, 'show single review');
         
    }

    /**
     * Get All Reviews 
     *
     * @return \Illuminate\Http\Response
     */
    public function showReviews(Request $request)
    { 
        $proid = (isset($request->proid)) ?  intval($request->proid) : 0 ; 

        $query =  Reviews::select('id','title','comment','rating','proid','userid');

        if(!empty($proid)){
            $query->where('proid',$proid);
        }
        $result = $query->get();
        
        return $this->sendResponse($result, 'All reviews of this  post');
        
    }
}
