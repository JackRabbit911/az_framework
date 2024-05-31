<?php

namespace Sys\Template;

use Az\Session\SessionInterface;

trait ComponentForm
{
    private function _render($data, $entity = null)
    {
        $data = $this->santize($data, $entity);
        $data = $this->validate($data);
        return view($this->view, $data);
    }

    private function santize($data, $entity = null)
    {
        $main = ['label', 'name', 'id', 'type', 'class', 'value', 'checked', 'placeholder', 'attributes', 'extra'];

        foreach ($data as $key => &$attribute) {
            if ($key === 'form' || (is_string($attribute) && is_string($key))) {
                continue;
            }

            if (is_int($key)) {
                unset($data[$key]);
                $data[$attribute] = [];
                $key = $attribute;
                $attribute = [];
            }

            if (!isset($attribute['name'])) {
                $attribute['name'] = $key;
            }

            if (!isset($attribute['label'])) {
                $attribute['label'] = ucfirst($attribute['name']);
            }

            if (isset($entity->$key)) {
                $attribute['value'] = $entity->$key;
            }

            $arr = [];

            foreach ($attribute as $k => &$v) {
                if (!in_array($k, $main) && $v !== 'checked') {
                    if (is_int($k)) {
                        $arr[] = $v;
                    } elseif ($v === true) {
                        $arr[] = $k;    
                    } elseif ($v !== false && !empty($v)) {
                        $arr[] = $k . '="' . $v . '"';
                    }

                    unset($attribute[$k]);
                } elseif ($v === 'checked') {
                    unset($attribute[$k]);
                    $attribute['checked'] = true;
                } elseif ($k === 'checked') {
                    $attribute['checked'] = $this->isTrue($v);
                }

                $attribute['attributes'] = implode(' ', $arr);
            }           
        }

        return $data;
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
