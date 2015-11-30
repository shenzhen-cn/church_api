<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bibile_model extends MY_Model {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
	}
	
	public function find_volumeName()
	{
		$this->db->select('id,directory as volumeName,testament');
		$this->db->from('church.bibile_book');
		return $this->db->get()->result();
	}

	public function look_volume($book_id,$chapter_id)
	{
		return $this->db->get_where('church.bible_section', array( 'book_id' => $book_id ,'chapter_id' =>$chapter_id))->result();
	}

	public function look_volume_note($book_id,$chapter_id)
	{
		return $this->db->get_where('church.bible_note', array( 'book_id' => $book_id ,'chapter_id' =>$chapter_id))->result();
	}

	public function count_chapter($book_id)
	{
		$this->db->select('count(distinct chapter_id) as count_chapter')->from('church.bible_section')->where('book_id', $book_id);

		return $this->db->get()->row();
		// $this->db->get()->row();
		// echo $this->db->last_query();exit();

	}
	public function volume_name($book_id)
	{
		return $this->db->get_where('church.bibile_book', array( 'id' => $book_id))->row();
	}
	
	public function onlineBibile($search_keyword, $limit, $offset)
	{
		if ( ! empty($search_keyword)) {

			$this->db->select('id,book_id,testament,chapter_id,section,content');
			$this->db->from('bible_section');
			$this->db->like('content',$search_keyword);
			// return $this->db->get()->result();
			$this->db->limit($limit, $offset);

			$data=$this->db->get();
			// var_dump($data);exit();
			$data_return = array();

			foreach($data->result_array() as $row_cer){
				$book_id = $row_cer['book_id'];

				$this->db->select('id,directory,testament');
				$this->db->from('bibile_book');
				$this->db->where('id' ,$book_id);
				$data1 = $this->db->get();
				
				foreach ($data1->result_array() as $row_cer2) {
					$row_cer['directory'] = $row_cer2['directory'];
					$data_return[] = $row_cer;
				}
			}

			return $data_return;

		}else {
			return false;
		}
	}

	public function count_all($search_keyword)
	{
		if (! empty($search_keyword)) {
			
			$this->db->select('*');
			$this->db->from('bible_section');
			$this->db->like('content',$search_keyword);
			$q = $this->db->get();
			// echo $this->db->last_query();exit;
	        return $q->num_rows();
		}
	}

	public function get_bibile_book_id_by_testament($testament)
	{
		if (!empty($testament)) {
			$this->db->select('id as book_id ,name');
			$this->db->from('bibile_book');
			$this->db->where('testament',$testament);
			return $this->db->get()->result();

		}else{
			return false;
		}
	}
	

	public function get_bible_section_by_book_id($book_id)
	{
		if (!empty($book_id)) {
			
			$this->db->select('chapter_id');
			$this->db->distinct('chapter_id');
			$this->db->from('bible_section');
			$this->db->where('book_id',$book_id);
			return $this->db->get()->result();

		}else{
			return false;
		}
	}
	
}
	
	
	
	
	
	
	
	
	
