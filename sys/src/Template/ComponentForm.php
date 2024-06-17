<?php

namespace Sys\Template;

use Az\Session\SessionInterface;

trait ComponentForm
{
    private function _render()
    {
        $data = $this->validate($this->attributes);
        $data = $this->prepare($data);
        
        // dd($data);
        return view($this->view, $data);
    }

    private function prepare($attributes)
    {
        $pattern = '[\[|\]|\.]';
        $attrs = [];

        foreach ($attributes as $key => &$attribute) {
            $arr_keys = preg_split($pattern, $key, -1, PREG_SPLIT_NO_EMPTY);

            while (!empty($arr_keys)) {
                $last_key = array_pop($arr_keys);
                $res[$last_key] = $attribute;
                $attribute = $res;
                $res = [];
            }

            $attrs = array_merge_recursive($attrs, $attribute);
        }

        return $attrs;
    }

    private function validate($data)
    {
        $session = container()->get(SessionInterface::class);

        $validationResponse = $session->pull('validation');

        if ($validationResponse) {
            foreach ($data as $key => &$attribute) {
                if (isset($validationResponse[$key])) {
                    $attribute = array_replace($attribute, $validationResponse[$key]);

                    if ($validationResponse[$key]['value'] === false) {
                        $attribute['checked'] = false;
                    } else {
                        $attribute['checked'] = true;
                    }
                }
            }           
        } 

        return $data;
    }

    private function isTrue($attr) {
        $yes = ["1", "yes", "on", "true", "checked", 1];
        return (in_array($attr, $yes) || $attr === true) ? true : false;
    }
}
