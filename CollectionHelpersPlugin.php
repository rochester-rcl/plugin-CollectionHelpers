<?php

class CollectionHelpersPlugin extends Omeka_Plugin_AbstractPlugin
{
    protected $_hooks = [
        'public_items_show'
    ];

    protected $_filters = [
        'item_citation'
    ];

    public function hookPublicItemsShow($args)
    {

    }

    public function getAccessed()
    {
        return 'accessed ' . date('F jS\, Y');
    }

    public function getCollection($item)
    {
        $collection = get_collection_for_item($item);
        if ($collection) {
            return metadata($collection, ['Dublin Core', 'Title']);
        }
        return "";
    }

    public function getSiteTitle()
    {
        $title = option('site_title');
        if ($title) {
            return "<em>{$title}</em>";
        }
        return "";
    }

    public function getURL($item)
    {
        return '<span class="citation-url">'.html_escape(record_url($item, null, true)).'</span>';
    }

    public function stripCreatorLinks($creator) 
    {
        if ($creator) {
            return "<span>" . strip_tags($creator) . "</span>";
        }
    }

    public function getCreators($item) 
    {
        $creators = metadata($item, ['Dublin Core', 'Creator'], ['all' => true]);
        if (count($creators) > 0) {
            return implode(', ', array_map("CollectionHelpersPlugin::stripCreatorLinks", $creators));
        }
        return NULL;
    }

    public static function formatCitation($val) {
        if ($val === NULL) {
            return false;
        }

        if ($val === "") {
            return false;
        }

        return true;
    }

    public function filterItemCitation($citation, $args)
    {
        $item = $args['item'];
        $elements = item_type_elements($item);
        $_citation = [];
        $_citation[0] = $this->getCreators($item);
        $_citation[2] = '"' . metadata($item, ['Dublin Core', 'Title']) . '."';
        $_citation[3] = $this->getCollection($item);
        $_citation[4] = $this->getSiteTitle();
        $_citation[5] = $this->getAccessed();
        $_citation[6] = $this->getURL($item);
        return implode(', ', array_filter($_citation, "CollectionHelpersPlugin::formatCitation"));
    }
}
