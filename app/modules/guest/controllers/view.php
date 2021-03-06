<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class View
 * @package Modules\Guest\Controllers
 * @property CI_Session $session
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 * @property Mdl_Items $mdl_items
 * @property Mdl_Quotes $mdl_quotes
 * @property Mdl_Quote_Items $mdl_quote_items
 * @property Mdl_Quote_Tax_Rates $mdl_quote_tax_rates
 * @property Mdl_Payment_Methods $mdl_payment_methods
 */
class View extends Base_Controller
{
    /**
     * Returns the web view for an invoice based on the given URL key
     * @param $url_key
     */
    public function invoice($url_key)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('invoice_url_key', $url_key)->get();

        if ($invoice->num_rows() == 1) {
            $this->load->model('invoices/mdl_items');
            $this->load->model('invoices/mdl_invoice_tax_rates');
            $this->load->model('payment_methods/mdl_payment_methods');

            $invoice = $invoice->row();

            if ($this->session->userdata('user_type') <> 1 and $invoice->invoice_status_id == 2) {
                $this->mdl_invoices->mark_viewed($invoice->invoice_id);
            }

            $payment_method = $this->mdl_payment_methods->where('payment_method_id',
                $invoice->payment_method)->get()->row();
            if ($invoice->payment_method == 0) {
                $payment_method = null;
            }

            $data = array(
                'invoice' => $invoice,
                'items' => $this->mdl_items->get_items_and_replace_vars($invoice->invoice_id),
                'invoice_tax_rates' => $this->mdl_invoice_tax_rates->where('invoice_id',
                    $invoice->invoice_id)->get()->result(),
                'invoice_url_key' => $url_key,
                'flash_message' => $this->session->flashdata('flash_message'),
                'payment_method' => $payment_method
            );

            $this->load->view('invoice_templates/public/' . $this->mdl_settings->setting('public_invoice_template') . '.php',
                $data);
        }
    }

    /**
     * Returns the generated PDF of the invoice based on the given ID
     * @param $url_key
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_invoice_pdf($url_key, $stream = true, $invoice_template = null)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->guest_visible()->where('url_key', $url_key)->get();

        if ($invoice->num_rows() == 1) {
            $invoice = $invoice->row();

            if (!$invoice_template) {
                $invoice_template = $this->mdl_settings->setting('pdf_invoice_template');
            }

            $this->load->helper('pdf');

            generate_invoice_pdf($invoice->invoice_id, $stream, $invoice_template, 1);
        }
    }

    /**
     * Returns the web view for an invoice based on the given URL key
     * @param $url_key
     */
    public function quote($url_key)
    {
        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->guest_visible()->where('url_key', $url_key)->get();

        if ($quote->num_rows() == 1) {
            $this->load->model('quotes/mdl_quote_items');
            $this->load->model('quotes/mdl_quote_tax_rates');


            $quote = $quote->row();

            if ($this->session->userdata('user_type') <> 1 and $quote->quote_status_id == 2) {
                $this->mdl_quotes->mark_viewed($quote->quote_id);
            }

            $data = array(
                'quote' => $quote,
                'items' => $this->mdl_quote_items->where('id', $quote->id)->get()->result(),
                'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote->id)->get()->result(),
                'quote_url_key' => $url_key,
                'flash_message' => $this->session->flashdata('flash_message')
            );

            $this->load->view('quote_templates/public/' . $this->mdl_settings->setting('public_quote_template') . '.php',
                $data);
        }
    }

    /**
     * Returns the generated PDF of the quote based on the given ID
     * @param $url_key
     * @param bool $stream
     * @param null $quote_template
     */
    public function generate_quote_pdf($url_key, $stream = true, $quote_template = null)
    {
        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->guest_visible()->where('url_key', $url_key)->get();

        if ($quote->num_rows() == 1) {
            $quote = $quote->row();

            if (!$quote_template) {
                $quote_template = $this->mdl_settings->setting('pdf_quote_template');
            }

            $this->load->helper('pdf');

            generate_quote_pdf($quote->quote_id, $stream, $quote_template);
        }
    }

    /**
     * Approves a quote based on the given URL key
     * @param $url_key
     */
    public function approve_quote($url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->approve_quote_by_key($url_key);
        email_quote_status($this->mdl_quotes->where('quotes.url_key', $url_key)->get()->row()->quote_id,
            "approved");

        redirect('guest/view/quote/' . $url_key);
    }

    /**
     * Rejects a quote based on the given URL key
     * @param $url_key
     */
    public function reject_quote($url_key)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->reject_quote_by_key($url_key);
        email_quote_status($this->mdl_quotes->where('quotes.url_key', $url_key)->get()->row()->quote_id,
            "rejected");

        redirect('guest/view/quote/' . $url_key);
    }
}
