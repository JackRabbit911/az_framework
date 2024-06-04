<?php declare(strict_types=1);

namespace Sys\Form;

use Sys\Template\Component;
use Sys\Template\ComponentForm;

class Form extends Component
{
    use ComponentForm;

    protected string $view;

    protected array $inputs = [
        'text', 'button','checkbox', 'color','date', 'datetime-local',
        'email', 'file', 'hidden', 'image', 'month','number','password',
        'radio', 'range', 'reset', 'search', 'submit', 'tel', 'time',
        'url', 'week', 'textarea', 'select',
    ];

    // protected array $attributes = [
    //     'label', 'name', 'id', 'type', 'class', 'value', 'checked',
    //     'placeholder', 'attributes', 'extra',
    // ];

    // public string $title = '';
    protected array $data = [];

    public function render(mixed $entity = null)
    {
        $data = $this->santize($this->data, $entity);
        $data = $this->validate($data);

        return view($this->view, $data);
    }

    public function __call($func, $arguments)
    {
        $name = array_shift($arguments) ?? $func;

        if (in_array($func, $this->inputs)) {

            return $this->setInput($func, $name, $arguments[0] ?? []);
        }

        return $this->setAttr($func, $name);
    }

    public function __isset($name)
    {
        return (isset($this->$name) || isset($this->_data[$name]));
    }

    public function __get($name)
    {
        return $this->$name ?? $this->data[$name] ?? null;
    }

    public function form($view)
    {
        $this->view = $view;
        $this->data['form'] = [];
        return $this;
    }

    public function title(?string $title = null)
    {
        if ($title) {
            $this->data['title'] = $title;
            return $this;
        }

        return $this->data['title'];
    }

    private function setInput(string $func, ?string $name = null, array $attributes = [])
    {
        $name = (($name)) ?: $func;
        $attributes['type'] = $func;
        
        if (!isset($attributes['label'])) {
            $attributes['label'] = ucfirst($name);
        }

        $this->data[$name] = $attributes;

        return $this;
    }

    private function setAttr($func, $value)
    {
        $name = array_key_last($this->data);
        $this->data[$name][$func] = $value;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
