<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Datatables_model extends CI_Model
{
	protected function _get_datatables_query($table,$column_order,$column_search,$order)
    {
        $this->db->from($table);

        $i = 0;

        foreach ($column_search as $item) // loop column
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {

                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if(count($column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if(isset($_POST['order'])) // here order processing
        {
            // print_r($_POST['order']);
            // print_r($column_order);
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($order))
        {
            $order = $order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($table,$column_order,$column_search,$order,$data,$wherein='')
    {
        $this->_get_datatables_query($table,$column_order,$column_search,$order);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $this->db->where($data);
        if($wherein)
        {
            $this->db->where_in('id_instansi',$wherein);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($table,$column_order,$column_search,$order,$data=array(),$wherein='')
    {
        $this->_get_datatables_query($table,$column_order,$column_search,$order);
        $this->db->where($data);
        if($wherein)
        {
            $this->db->where_in('input_by',$wherein);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($table,$data=array(),$wherein='')
    {
        $this->db->where($data);
        if($wherein)
        {
            $this->db->where_in('input_by',$wherein);
        }
        $this->db->from($table);
        return $this->db->count_all_results();
    }


  
}