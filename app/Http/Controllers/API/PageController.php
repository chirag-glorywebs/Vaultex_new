<?php

namespace App\Http\Controllers\API;
use  App\Models\Page;
use App\Http\Controllers\API\BaseController;


class PageController extends BaseController
{
     
    /**
     * Get details pageby slug
     *
     * @return \Illuminate\Http\Response
    */
    public function show($slug)
    { 
       
        $data =  Page::where('slug',$slug)->where('status',1)->take(1)->get();
        
        if(!$data->isEmpty()){
            foreach ($data as $data) {
                $data->banner  = asset($data->banner);
                $data->video = asset($data->video);
            }
            return $this->sendResponse($data, $data->page_name.' page');
        }else{
            return $this->sendError('404 page not found.', $data);
        }
        
    }
  
}
