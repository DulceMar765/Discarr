<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePortfolioTableToPortfolios extends Migration
{
    public function up()
    {
        Schema::rename('portfolio', 'portfolios'); // Renombrar la tabla
    }

    public function down()
    {
        Schema::rename('portfolios', 'portfolio'); // Revertir el cambio si es necesario
    }
}
