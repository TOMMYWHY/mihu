<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Question extends Model
{
    public function add(){

//        dd(rq());
        /*验证是否登录*/
//        dd(user_instance()->is_logoed_in());
        if (!user_instance()->is_logoed_in()){
            return['status'=>0,'msg'=>'login is required!'];
        }

        if (!rq('title')){
            return['status'=>0,'msg'=>'title is required!'];
        }else{
            $this->title=rq('title');
        }

        $this->user_id=session('user_id');

        if (rq('desc')){
            $this->desc=rq('desc');
        }
        return $this->save()?
            ['status'=>1,'id'=>$this->id]:
            ['status'=>0,'msg'=>'db insert failed!'];
    }

    public function change(){
        if(!user_instance()->is_logoed_in()){
            return['status'=>0,'msg'=>'login is required!'];
        }

        if (!rq('id')){
            return['status'=>0,'msg'=>'id is required!'];
        }else{
            $question_ins=$this->find(rq('id'));
        }

//        dd($question_ins);
        if ($question_ins->user_id!=session('user_id')){
            return['status'=>0,'msg'=>'permission denied!'];
        }

        if (rq('title')){
            $question_ins->title=rq('title');
        }
        if (rq('desc')){
            $question_ins->desc=rq('desc');
        }

        return $question_ins->save()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'db insert failed'];

    }

    public function remove(){
        if(!user_instance()->is_logoed_in()){
            return['status'=>0,'msg'=>'login is required!'];
        }
        if (!rq('id')){
            return['status'=>0,'msg'=>'id is required!'];
        }else{
            $question_ins=$this->find(rq('id'));
        }
        /*$question_ins 返回一个对象 或是 null if判断将对象转换成ture null则为false */
        if (!$question_ins){
            return['status'=>0,'msg'=>'id is not exists!'];
        }
        if ($question_ins->user_id!=session('user_id')){
            return['status'=>0,'msg'=>'permission denied!'];
        }

        return $question_ins->delete()?
            ['status'=>1]:
            ['status'=>0,'msg'=>'db delete failed'];


    }

    public function read(){
        if (rq('id')){
            $question_ins=$this->find(rq('id'));
            return['status'=>1,'data_question'=>$question_ins];
        }

//        $limit=rq('limit')?:15;
//        $skip=(rq('page')?rq('page')-1:0)*$limit;

        list($limit,$skip)=paginate(rq('page'),rq('limit'));

        $result= $this
            ->orderBy('created_at')
            ->limit($limit)
            ->skip($skip)
            ->get(['id','title','desc','user_id','created_at','updated_at'])
            ->keyBy('id');

        return ['status'=>1,'data_question'=>$result];
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
}
