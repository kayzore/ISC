<?php

namespace ISC\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ISCUserBundle extends Bundle
{
	public function getParent()
  	{
    	return 'FOSUserBundle';
  	}
}
