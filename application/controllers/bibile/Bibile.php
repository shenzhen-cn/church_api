<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Bibile extends MY_Controller {

	const DEFAULT_LIMIT = 10;
	const MAX_LIMIT = 20;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('bibile/bibile_model');

	}

	public function find_volumeName()
	{
		
	    $results = $this->bibile_model->find_volumeName();
		$this->response(array('results' => $results));
	}

	public function look_volume()
	{
		
		$book_id  =	$_REQUEST['book_id'] ? $_REQUEST['book_id'] : "" ;
		$chapter_id  =	$_REQUEST['chapter_id'] ? $_REQUEST['chapter_id'] :  "" ;

			// var_dump($book_id);exit;
	    $volume_name = $this->bibile_model->volume_name($book_id);
	    $bible_section = $this->bibile_model->look_volume($book_id,$chapter_id);
	    $bible_note = $this->bibile_model->look_volume_note($book_id,$chapter_id);
	    $count_chapter = $this->bibile_model->count_chapter($book_id);
	    // var_dump($count_chapter);exit();
	    
	    // var_dump($count_chapter->count_chapter);exit;

		$this->response(array('bible_section' => $bible_section ,'bible_note' => $bible_note,'count_chapter' =>$count_chapter->count_chapter,'volume_name'=>$volume_name->name ));

	}

	public function onlineBibile()
	{
		$search_keyword  =	$_REQUEST['search_keyword'];
		// var_dump($search_keyword);exit;
		$limit = $this->get('limit') ? $this->get('limit') : self::DEFAULT_LIMIT;
		if($limit > self::MAX_LIMIT) $limit = self::DEFAULT_LIMIT;

		$page = $this->get('page') ? $this->get('page') : 1;
		if($page == 0) $page = 1;

		if( ! $total = $this->bibile_model->count_all($search_keyword))
		{
			$this->response(array('message'=>'没有找到！你想要<b>【'.$search_keyword.'】</b>相关的经文！'));
			return;
		}

		$this->load->helper('util_helper');
		$pagination = get_pagination($total, $limit, $page);	

		if ( ! $bibile_seciton = $this->bibile_model->onlineBibile($search_keyword, $pagination['limit'], $pagination['offset'])) {

			$this->response(array('message'=>'没有找到，你想要相关的经文！'));
			return;
		}


		$this->response(array('total' => $total, 'results' => $bibile_seciton));


	}

	public function get_bibile_book_id_by_testament()
	{
		$testament = $this->input->get('testament') ? $this->input->get('testament') : "";
		// var_dump($testament);exit();
		$results = $this->bibile_model->get_bibile_book_id_by_testament($testament);

		if (!$results) {
			$this->response(array('status_code'=>'400'));
			return;
		}

		$this->response(array('status_code' => '200', 'results' => $results));

		
	}

	public function get_bible_section_by_book_id()
	{
		$book_id = $this->input->get('book_id') ? $this->input->get('book_id') : "";
		// var_dump($book_id);exit();
		$results = $this->bibile_model->get_bible_section_by_book_id($book_id);

		if (!$results) {
			$this->response(array('status_code'=>'400'));
			return;
		}

		$this->response(array('status_code' => '200', 'results' => $results));
	}

	
}
