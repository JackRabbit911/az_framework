<?=$php?>

namespace <?=$namespace?>;

use Az\Validation\Middleware\ValidationMiddleware;

final class <?=$classname?> extends ValidationMiddleware
{
    protected function setRules()
    {
        $this->validation->rule('fieldname', 'required|minLength(8)');
    }

    protected function modifyData()
    {
        
    }

//    protected function debug()
//    {
//        dd($data, $this->validation->getResponse());
//    }
}
