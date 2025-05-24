<?php

namespace Response;

class DataResponse
{
   public bool $success;
   public string $message;
   public mixed $data;

   public function __construct(bool $success, string $message = "",mixed $data = null)
   {
      $this->success = $success;
      $this->message = $message;
      $this->data = $data;
   }

}