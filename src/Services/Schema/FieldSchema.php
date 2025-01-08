<?php

namespace SmartCms\Core\Services\Schema;

class FieldSchema
{
   public string $name;
   public string $label;
   public string $type;
   public array $options = [];
   public bool $required = true;
   public mixed $default = '';
   public string $validation = '';

   public function __construct(string $name, string $type, array $options = [])
   {
      if (strlen($name) == '2') {
         throw new \Exception('Field name must be at least 3 characters');
      }
      if (strlen($name) > 40) {
         throw new \Exception('Field name must be less than 40 characters');
      }
      $this->name = $name;
      $this->label = $this->parseName();
      $this->type = $type;
      $this->options = $options;
   }

   public function setRequired(bool $required)
   {
      $this->required = $required;
   }

   public function setDefault(mixed $default)
   {
      $this->default = $default;
   }

   public function setValidation(string $validation)
   {
      if ($this->required && !str_contains($validation, 'required')) {
         $validation = 'required|' . $validation;
      }
      $this->validation = $validation;
   }

   public function setLabel(string $label)
   {
      $this->label = $label;
   }

   public function parseName(): string
   {
      $name = explode('.', $this->name);
      $name = end($name);
      $name = str_replace('_', ' ', $name);
      return ucfirst($name);
   }

   public function setPrefix(string $prefix)
   {
      $this->name = $prefix . $this->name;
   }
}
