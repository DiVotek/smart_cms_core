<?php

namespace SmartCms\Core\Exceptions;

class VariableException extends \Exception
{
   public static function nameNotExists(string $name): self
   {
      return new self("Variable name not exists in variable: $name");
   }

   public static function typeNotExists(string $name): self
   {
      return new self("Variable type not found in variable types: $name");
   }


}
