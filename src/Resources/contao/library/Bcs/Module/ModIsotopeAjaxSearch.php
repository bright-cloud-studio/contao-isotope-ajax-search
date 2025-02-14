<?php

namespace Bcs\Module;

use Contao\BackendTemplate;
use Contao\Controller;
use Contao\Database;
use Contao\Input;
use Contao\System;
use Contao\FrontendUser;

use Isotope\Interfaces\IsotopeProduct;
use Isotope\Isotope;
use Isotope\DatabaseUpdater;

use Isotope\Model\Attribute;
use Isotope\Model\AttributeOption;
use Isotope\Model\Attribute\TextField;
use Isotope\Model\Product;

use Isotope\Backend\Attribute\DatabaseUpdate;

class ModIsotopeAjaxSearch extends \Contao\Module
{

    /* Default Template */
    protected $strTemplate = 'mod_isotope_ajax_search';

    /* Construct function */
    public function __construct($objModule, $strColumn='main')
    {
        parent::__construct($objModule, $strColumn);
    }

    /* Generate function */
    public function generate()
    {
        $request = System::getContainer()->get('request_stack')->getCurrentRequest();

        if ($request && System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request))
        {
            $objTemplate = new BackendTemplate('be_wildcard');
 
            $objTemplate->wildcard = '### ' . mb_strtoupper($GLOBALS['TL_LANG']['FMD']['salsify_importer'][0]) . ' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&table=tl_module&act=edit&id=' . $this->id;
 
            return $objTemplate->parse();
        }
        return parent::generate();
    }

    // When Page Loads
    protected function compile()
    {
        // Check if we have a SKU in our URL
        if (Input::get('sku') != '') {
            
            // Convert SKU csv into an array
            $skus = (explode(",", Input::get('sku')));
            // Store our results
            $results = array();
            
            foreach($skus as $sku) {
                
                // Get Product or Variant
                $product = Product::findOneBy(['tl_iso_product.sku=?'],[$sku]);
                
                if($product) {
                    // Add attributes to array
                    $details = array();
                    $details['name'] = $product->name;
                    $details['id'] = $product->id;
                    $details['sku'] = $product->sku;
                    
                    $results[$details['sku']] = $details;
                }
                
                
                
            }
            
            // Add our attributes to the template so we can see it on the front end
            $this->Template->results = $results;


        }
    }

}
