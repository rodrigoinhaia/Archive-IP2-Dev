<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Custom_Fields
 * @package Modules\CustomFields\Controllers
 *
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property Layout $layout
 * @property Mdl_Custom_Fields $mdl_custom_fields
 */
class Custom_Fields extends User_Controller
{
    /**
     * Custom_Fields constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_custom_fields');
    }

    /**
     * Returns the index page with all custom fields
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_custom_fields->paginate(site_url('custom_fields/index'), $page);
        $custom_fields = $this->mdl_custom_fields->result();

        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->buffer('content', 'custom_fields/index');
        $this->layout->render();
    }

    /**
     * Returns the form
     * If an ID was provided the form will be filled with the data of the custom field
     * for the given ID and can be used as an edit form.
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('custom_fields');
        }

        if ($this->mdl_custom_fields->run_validation()) {
            $this->mdl_custom_fields->save($id);
            redirect('custom_fields');
        }

        if ($id && !$this->input->post('btn_submit')) {
            if (!$this->mdl_custom_fields->prep_form($id)) {
                show_404();
            }
        }

        $this->layout->set('custom_field_tables', $this->mdl_custom_fields->custom_tables());
        $this->layout->set('custom_field_types', $this->mdl_custom_fields->custom_types());
        $this->layout->buffer('content', 'custom_fields/form');
        $this->layout->render();
    }

    /**
     * Deletes a custom field from the database based on the given ID
     * @param $id
     */
    public function delete($id)
    {
        if ($this->mdl_custom_fields->delete($id)) {
            set_alert('success', lang('custom_field_deleted'));
            redirect('custom_fields');
        } else {
            set_alert('danger', lang('custom_field_has_data'));
            redirect('custom_fields');
        }
    }

}
