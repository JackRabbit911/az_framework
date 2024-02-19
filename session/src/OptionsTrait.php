<?php

namespace Az\Session;

trait OptionsTrait
{
    public function options(array $options): void
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {               
                $this->$key = array_replace_recursive($this->$key, $value);
            } else {
                $this->$key = $value;
            }        
        }
    }
}
