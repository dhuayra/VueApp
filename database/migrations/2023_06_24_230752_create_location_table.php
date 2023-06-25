<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up(): void
    {
        // Schema::create('countries', function (Blueprint $table) {
        //     $table->char('id', 2)->index();
        //     $table->string('description');
        //     $table->boolean('active')->default(true);
        // });

        // DB::table('countries')->insert([
        //     ['id' => 'PK', 'description' => 'PAKISTAN'],
        //     ['id' => 'PW', 'description' => 'PALAU'],
        //     ['id' => 'PS', 'description' => 'PALESTINIAN TERRITORY, Occupied'],
        //     ['id' => 'PA', 'description' => 'PANAMA'],
        //     ['id' => 'PG', 'description' => 'PAPUA NEW GUINEA'],
        //     ['id' => 'PY', 'description' => 'PARAGUAY'],
        //     ['id' => 'PE', 'description' => 'PERU'],
        //     ['id' => 'PH', 'description' => 'PHILIPPINES'],
        //     ['id' => 'PN', 'description' => 'PITCAIRN'],
        //     ['id' => 'PL', 'description' => 'POLAND'],
        //     ['id' => 'PT', 'description' => 'PORTUGAL'],
        //     ['id' => 'PR', 'description' => 'PUERTO RICO'],
        // ]);

        // Schema::create('departments1', function (Blueprint $table) {
        //     $table->char('id', 2)->index();
        //     $table->string('description');
        //     $table->boolean('active')->default(true);
        // });
        // DB::table('departments1')->insert([
        //     ['id' => '01', 'description' => 'AMAZONAS'],
        //     ['id' => '02', 'description' => 'ÁNCASH'],
        //     ['id' => '03', 'description' => 'APURIMAC'],
        //     ['id' => '04', 'description' => 'AREQUIPA'],
        //     ['id' => '05', 'description' => 'AYACUCHO'],
        //     ['id' => '06', 'description' => 'CAJAMARCA'],
        //     ['id' => '07', 'description' => 'CALLAO'],
        //     ['id' => '08', 'description' => 'CUSCO'],
        //     ['id' => '09', 'description' => 'HUANCAVELICA'],
        //     ['id' => '10', 'description' => 'HUÁNUCO'],
        //     ['id' => '11', 'description' => 'ICA'],
        //     ['id' => '12', 'description' => 'JUNÍN'],
        //     ['id' => '13', 'description' => 'LA LIBERTAD'],
        //     ['id' => '14', 'description' => 'LAMBAYEQUE'],
        //     ['id' => '15', 'description' => 'LIMA'],
        //     ['id' => '16', 'description' => 'LORETO'],
        //     ['id' => '17', 'description' => 'MADRE DE DIOS'],
        //     ['id' => '18', 'description' => 'MOQUEGUA'],
        //     ['id' => '19', 'description' => 'PASCO'],
        //     ['id' => '20', 'description' => 'PIURA'],
        //     ['id' => '21', 'description' => 'PUNO'],
        //     ['id' => '22', 'description' => 'SAN MARTIN'],
        //     ['id' => '23', 'description' => 'TACNA'],
        //     ['id' => '24', 'description' => 'TUMBES'],
        //     ['id' => '25', 'description' => 'UCAYALI'],
        // ]);

        Schema::create('provinces', function (Blueprint $table) {
            $table->char('id', 4)->index();
            $table->char('department_id', 2);
            $table->string('description');
            $table->boolean('active')->default(true);

            $table->foreign('department_id')->references('id')->on('departments1');
        });
        DB::table('provinces')->insert([
            ['id' => '0101', 'description' => 'Chachapoyas', 'department_id' => '01'],
            ['id' => '0102', 'description' => 'Bagua', 'department_id' => '01'],
            ['id' => '0103', 'description' => 'Bongará', 'department_id' => '01'],
            ['id' => '0104', 'description' => 'Condorcanqui', 'department_id' => '01'],
            ['id' => '0105', 'description' => 'Luya', 'department_id' => '01'],
            ['id' => '0106', 'description' => 'Rodríguez de Mendoza', 'department_id' => '01'],
            ['id' => '0107', 'description' => 'Utcubamba', 'department_id' => '01'],
            ['id' => '0201', 'description' => 'Huaraz', 'department_id' => '02'],
            ['id' => '0202', 'description' => 'Aija', 'department_id' => '02'],
            ['id' => '0203', 'description' => 'Antonio Raymondi', 'department_id' => '02'],
            ['id' => '0204', 'description' => 'Asunción', 'department_id' => '02'],
            ['id' => '0205', 'description' => 'Bolognesi', 'department_id' => '02'],
            ['id' => '0206', 'description' => 'Carhuaz', 'department_id' => '02'],
            ['id' => '0207', 'description' => 'Carlos Fermín Fitzcarrald', 'department_id' => '02'],
            ['id' => '0208', 'description' => 'Casma', 'department_id' => '02'],
            ['id' => '0209', 'description' => 'Corongo', 'department_id' => '02'],
            ['id' => '0210', 'description' => 'Huari', 'department_id' => '02'],
        ]);

        Schema::create('districts', function (Blueprint $table) {
            $table->char('id', 6)->index();
            $table->char('province_id', 4);
            $table->string('description');
            $table->boolean('active')->default(true);

            $table->foreign('province_id')->references('id')->on('provinces');
        });
        DB::table('districts')->insert([
            ['id' => '010101', 'description' => 'Chachapoyas', 'province_id' => '0101'],
            ['id' => '010102', 'description' => 'Asunción', 'province_id' => '0101'],
            ['id' => '010103', 'description' => 'Balsas', 'province_id' => '0101'],
            ['id' => '010104', 'description' => 'Cheto', 'province_id' => '0101'],
            ['id' => '010105', 'description' => 'Chiliquin', 'province_id' => '0101'],
            ['id' => '010106', 'description' => 'Chuquibamba', 'province_id' => '0101'],
            ['id' => '010107', 'description' => 'Granada', 'province_id' => '0101'],
            ['id' => '010108', 'description' => 'Huancas', 'province_id' => '0101'],
            ['id' => '010109', 'description' => 'La Jalca', 'province_id' => '0101'],
            ['id' => '010110', 'description' => 'Leimebamba', 'province_id' => '0101'],
            ['id' => '010111', 'description' => 'Levanto', 'province_id' => '0101'],
            ['id' => '010112', 'description' => 'Magdalena', 'province_id' => '0101'],
            ['id' => '010113', 'description' => 'Mariscal Castilla', 'province_id' => '0101'],
            ['id' => '010114', 'description' => 'Molinopampa', 'province_id' => '0101'],
            ['id' => '010115', 'description' => 'Montevideo', 'province_id' => '0101'],
            ['id' => '010116', 'description' => 'Olleros', 'province_id' => '0101'],
            ['id' => '010117', 'description' => 'Quinjalca', 'province_id' => '0101'],
            ['id' => '010118', 'description' => 'San Francisco de Daguas', 'province_id' => '0101'],
            ['id' => '010119', 'description' => 'San Isidro de Maino', 'province_id' => '0101'],
            ['id' => '010120', 'description' => 'Soloco', 'province_id' => '0101'],
            ['id' => '010121', 'description' => 'Sonche', 'province_id' => '0101'],
            ['id' => '010201', 'description' => 'Bagua', 'province_id' => '0102'],
            ['id' => '010202', 'description' => 'Aramango', 'province_id' => '0102'],
            ['id' => '010203', 'description' => 'Copallin', 'province_id' => '0102'],
            ['id' => '010204', 'description' => 'El Parco', 'province_id' => '0102'],
            ['id' => '010205', 'description' => 'Imaza', 'province_id' => '0102'],
            ['id' => '010206', 'description' => 'La Peca', 'province_id' => '0102'],
            ['id' => '010301', 'description' => 'Jumbilla', 'province_id' => '0103'],
            ['id' => '010302', 'description' => 'Chisquilla', 'province_id' => '0103'],
            ['id' => '010303', 'description' => 'Churuja', 'province_id' => '0103'],
            ['id' => '010304', 'description' => 'Corosha', 'province_id' => '0103'],
            ['id' => '010305', 'description' => 'Cuispes', 'province_id' => '0103'],
            ['id' => '010306', 'description' => 'Florida', 'province_id' => '0103'],
            ['id' => '010307', 'description' => 'Jazan', 'province_id' => '0103'],
            ['id' => '010308', 'description' => 'Recta', 'province_id' => '0103'],
            ['id' => '010309', 'description' => 'San Carlos', 'province_id' => '0103'],
            ['id' => '010310', 'description' => 'Shipasbamba', 'province_id' => '0103'],
            ['id' => '010311', 'description' => 'Valera', 'province_id' => '0103'],
            ['id' => '010312', 'description' => 'Yambrasbamba', 'province_id' => '0103'],
            ['id' => '010401', 'description' => 'Nieva', 'province_id' => '0104'],
            ['id' => '010402', 'description' => 'El Cenepa', 'province_id' => '0104'],
            ['id' => '010403', 'description' => 'Río Santiago', 'province_id' => '0104'],
            ['id' => '010501', 'description' => 'Lamud', 'province_id' => '0105'],
            ['id' => '010502', 'description' => 'Camporredondo', 'province_id' => '0105'],
            ['id' => '010503', 'description' => 'Cocabamba', 'province_id' => '0105'],
            ['id' => '010504', 'description' => 'Colcamar', 'province_id' => '0105'],
            ['id' => '010505', 'description' => 'Conila', 'province_id' => '0105'],
            ['id' => '010506', 'description' => 'Inguilpata', 'province_id' => '0105'],
            ['id' => '010507', 'description' => 'Longuita', 'province_id' => '0105'],
            ['id' => '010508', 'description' => 'Lonya Chico', 'province_id' => '0105'],
            ['id' => '010509', 'description' => 'Luya', 'province_id' => '0105'],
            ['id' => '010510', 'description' => 'Luya Viejo', 'province_id' => '0105'],
            ['id' => '010511', 'description' => 'María', 'province_id' => '0105'],
            ['id' => '010512', 'description' => 'Ocalli', 'province_id' => '0105'],
            ['id' => '010513', 'description' => 'Ocumal', 'province_id' => '0105'],
            ['id' => '010514', 'description' => 'Pisuquia', 'province_id' => '0105'],
            ['id' => '010515', 'description' => 'Providencia', 'province_id' => '0105'],
            ['id' => '010516', 'description' => 'San Cristóbal', 'province_id' => '0105'],
            ['id' => '010517', 'description' => 'San Francisco de Yeso', 'province_id' => '0105'],
            ['id' => '010518', 'description' => 'San Jerónimo', 'province_id' => '0105'],
            ['id' => '010519', 'description' => 'San Juan de Lopecancha', 'province_id' => '0105'],
            ['id' => '010520', 'description' => 'Santa Catalina', 'province_id' => '0105'],
            ['id' => '010521', 'description' => 'Santo Tomas', 'province_id' => '0105'],
            ['id' => '010522', 'description' => 'Tingo', 'province_id' => '0105'],
            ['id' => '010523', 'description' => 'Trita', 'province_id' => '0105'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('departments1');
        Schema::dropIfExists('countries');
    }
};
