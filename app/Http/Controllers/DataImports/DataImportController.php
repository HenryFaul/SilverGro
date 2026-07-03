<?php

namespace App\Http\Controllers\DataImports;

use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Imports\ImportProducts;
use App\Imports\NewTransaction;
use App\Imports\OldTransactionImport;
use App\Imports\SupplierImport;
use App\Imports\TransporterImport;
use App\Models\Customer;
use App\Models\OldTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transporter;
use App\Models\TransportTransaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Inertia\Response;
use Inertia\ResponseFactory;
use Maatwebsite\Excel\Facades\Excel;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 10000);

class DataImportController extends Controller
{
    //

    public function index(): Response|ResponseFactory
    {


        return inertia(
            'DataImport/Index',
            [
                'Imports' => 'Import Data',

            ]
        );

    }

    public function import(Request $request): RedirectResponse
    {
        $message = ''; // Initialize message variable

        // Strategy: Try multiple methods to get the uploaded file
        // Method 1: Standard Laravel file() method (works for most cases)
        $uploadedFile = $request->file('file');

        // Method 2: Check if file exists but is invalid (Inertia bug with CSV)
        if ((!$uploadedFile || !$uploadedFile->isValid() || empty($uploadedFile->getPathname()))
            && $request->hasFile('file')) {

            Log::warning('File exists but is invalid - attempting to extract from raw request');

            // Inertia sometimes breaks file uploads, especially for CSV
            // Try to extract the file from $_FILES directly
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['file']['tmp_name'];
                $originalName = $_FILES['file']['name'];
                $mimeType = $_FILES['file']['type'];
                $size = $_FILES['file']['size'];

                Log::info('Extracted file from $_FILES', [
                    'tmp_name' => $tmpName,
                    'name' => $originalName,
                    'type' => $mimeType,
                    'size' => $size,
                ]);

                // Create a new UploadedFile from the raw data
                $uploadedFile = new UploadedFile(
                    $tmpName,
                    $originalName,
                    $mimeType,
                    $_FILES['file']['error'],
                    true // test mode = false means it will move the file
                );
            }
        }

        // Validate we have a valid file
        if (!$uploadedFile || !($uploadedFile instanceof UploadedFile) || !$uploadedFile->isValid()) {
            // Check for PHP upload errors
            $errorMessage = 'File upload failed. Please try selecting the file again.';

            if (isset($_FILES['file']['error'])) {
                $phpError = $_FILES['file']['error'];
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini (Currently: ' . ini_get('upload_max_filesize') . '). File size: ' . ($_FILES['file']['size'] ?? 'unknown'),
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];

                if (isset($errorMessages[$phpError])) {
                    $errorMessage = $errorMessages[$phpError];
                }
            }

            Log::error('No valid file uploaded after all attempts', [
                'has_file' => $request->hasFile('file'),
                'files_array' => $_FILES ?? 'not set',
                'request_files' => $request->files->all(),
                'error_message' => $errorMessage,
            ]);

            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', $errorMessage);
            return redirect()->back();
        }

        $file_name = $uploadedFile->getClientOriginalName();

        Log::info('File validated successfully', [
            'filename' => $file_name,
            'size' => $uploadedFile->getSize(),
            'mime' => $uploadedFile->getMimeType(),
        ]);

        // Store the file temporarily on the LOCAL disk. The default filesystem
        // disk is S3 in production, but the import reads the file back via an
        // absolute local path (storage_path) and hands it to Excel::import, so
        // the temp file must live on local disk, not S3.
        $filePath = $uploadedFile->store('temp', 'local');
        $fullPath = storage_path('app/' . $filePath);

        if (!file_exists($fullPath)) {
            Log::error('File storage failed', ['path' => $fullPath]);
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Failed to store the uploaded file.');
            return redirect()->back();
        }

        try {
            switch ($file_name) {
                case "Silver Customer Import.xlsx":

                    $count_before = Customer::all()->count();

                    Excel::import(new CustomerImport(), $fullPath);

                    $count_after = Customer::all()->count();

                    $message = "Customer Count before: ".$count_before." count after: ".$count_after;

                    break;
            case "Silver Supplier Import.xlsx":

                $count_before = Supplier::all()->count();

                Excel::import(new SupplierImport(), $fullPath);

                $count_after = Supplier::all()->count();

                $message = "Supplier Count before: ".$count_before." count after: ".$count_after;

                break;

            case "Silver Transporter Import.xlsx":

                $count_before = Transporter::all()->count();

                Excel::import(new TransporterImport(), $fullPath);

                $count_after = Transporter::all()->count();

                $message = "Transporter Count before: ".$count_before." count after: ".$count_after;

                break;

            case "Silver Transaction Import.xlsx":

                $count_before = OldTransaction::all()->count();

                Excel::import(new OldTransactionImport(), $fullPath);

                $count_after = OldTransaction::all()->count();

                $message = "Transactions Count before: ".$count_before." count after: ".$count_after;

                break;

            case "20230606 Silvergro Products.csv":

                $count_before = Product::all()->count();

                Excel::import(new ImportProducts(), $fullPath);

                $count_after = Product::all()->count();

                $message = "Products Count before: ".$count_before." products after: ".$count_after;

                break;
            case "raw trans 1.csv":


                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(), $fullPath);

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            case "raw trans 2.csv":

                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(), $fullPath);

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            case "raw trans 3.csv":

                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(), $fullPath);

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            case "raw trans 4.csv":

                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(), $fullPath);

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            default:
                $message = "No acceptable file name found. Uploaded file: " . $file_name;
        }
        } catch (Exception $e) {
            // Clean up the temporary file on error
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            Log::error('Import error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Error importing file: ' . $e->getMessage());
            return redirect()->back();
        }

        // Clean up the temporary file
        if (isset($fullPath) && file_exists($fullPath)) {
            unlink($fullPath);
        }


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', $message);

        return redirect()->back();

    }
}

