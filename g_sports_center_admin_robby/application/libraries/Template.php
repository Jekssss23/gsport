<?php 

	Class Template {
		var $template_data = [];
		function set($name, $value){
			$this->template_data[$name] = $value;
		}
		function load($template = '', $view = '', $view_data=[], $return=FALSE){
			$this->CI=&get_instance();
			
			// Ensure $modal is always defined to prevent "Undefined variable" errors in templates
			if (!isset($view_data['modal'])) {
				$view_data['modal'] = '';
			}

			// Merge view_data into template_data so variables are available in the template file
			$this->template_data = array_merge($this->template_data, $view_data);

			$this->set('konten', $this->CI->load->view($view, $view_data, TRUE));
			return $this->CI->load->view($template, $this->template_data, $return);
		}
	}
