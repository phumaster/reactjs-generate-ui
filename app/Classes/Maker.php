<?php

namespace PhuMaster\Classes;

use PhuMaster\Contracts\MakerContract;
use PhuMaster\Classes\Form;

class Maker extends Form implements MakerContract {

  public function __construct($form = []) {
    parent::__construct($form);
  }

  public function form(): string {
    return <<<COMPONENT
{$this->renderComment()}

{$this->import()}

{$this->validate()}

{$this->warning()}

{$this->renderField()}

class {$this->getFormName()} extends Component {
  constructor(props) {
    super(props);
  }

{$this->shouldComponentUpdate()}

{$this->render()}
}

{$this->export()}
COMPONENT;
  }

}
