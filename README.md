Require PHP 7x
My SQL 5.7 +
server linux or hosting which available for create cronjob. 

System Configuation Requirement
- CPU 6+ cores
- Ram 12+ GB
- SSD from 20GB

- config vhost with document route is source/public
- composer install 
- update .env file. update app url, prefix, database information 
- run: php artisan migration
- run: php artisan db:seed
- run: php artisan storage:link
- run: php artisan jwt:secret
- chmod 777 for storage, boostrap folder

### Chạy dữ liệu địa phương tại Việt Nam
B1: php artisan migrate
B2: vào cấu hình import dữ liệu địa chính  chonnj file và import

### Các bước cập nhât menu, roles, permission
B1: Chạy php artisan db:seed để cập nhật menu, roles, permission chuẩn của dự án.
B2: Vào CMS admin, chỉnh sửa lại các menu, roles, permission mong muốn.
B3: Chạy các lệnh sau để backup lại các thay đổi vào seeder:
    php artisan gpgroup:generate_rpm_data
    Khi chạy lại generate menu, roles, permissions thì chạy php artisan db:seed
B4: Commit code để lưu lại những thay đổi


DB::beginTransaction();
try{ 
    
    DB::commit();
} catch(Exception $ex) {
    if(env('APP_ENV') !== 'production') dd($ex);
    DB::rollback();
    Log::error($ex->getMessage(), [
        'process' => '[create group plan]',
        'function' => __function__,
        'file' => basename(__FILE__),
        'line' => __line__,
        'path' => __file__,
        'error_message' => $ex->getMessage()
    ]);
}


public function XXX($school_id, Request $request) {

    if ($request->isMethod('post')) {

    } else {
        $school = School::where('id', $school_id)->with('branches', 'branches.classes', 'branches.classes.students')->first();
        return "Cau hinh danh muc thuoc";
    }   
}

{{route('', ['school_id' => $school->id])}} 


@push('scripts')
<script>

    

</script>

@endpush

use App\Scopes\YearScope;

public static function boot()
{
    parent::boot();
    self::creating(function ($model) {
        $now = date('Y-m-d H:i:s', time());
        $endSchoolYear = date('Y-m-d H:i:s', strtotime(\App\Admin\Helpers\ListHelper::listYear()[1]."-06-30 23:59:59"));
        $model->created_at = $now > $endSchoolYear ? $endSchoolYear : $now;
    });

    static::addGlobalScope(new YearScope);

}

### Fast updating
bash ./deploy/~deploy.sh

## PHPWord Documents
https://github.com/PHPOffice/PHPWord/tree/develop/samples

/* // Store the file name into variable
    $file = 'file.docx';
    $filename = 'file.docx';

    // Header content type
    header('Content-type: application/docx');

    header('Content-Disposition: inline; filename="' . $filename . '"');

    header('Content-Transfer-Encoding: binary');

    header('Accept-Ranges: bytes');

    // Read the file
    readfile($file); */
    // The location of the PDF file
        // on the server
        $filename = "file.pdf";
        
        // Header content type
        header("Content-type: application/pdf");
        
        header("Content-Length: " . filesize($filename));
        
        // Send the file to the browser.
        readfile($filename);
    //return 1;
    /* 



test dimulator 
test feature 2
commti develop