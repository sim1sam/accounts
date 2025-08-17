<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CustomerImportController extends Controller
{
    public function uploadForm()
    {
        return view('admin.customers.import');
    }

    public function sample()
    {
        $csv = "name,mobile,email,address,dob,delivery_class,kam\n" .
               "Acme Ltd,01700111222,contact@acme.test,\"12 Street, City\",,A,1\n" .
               "John Doe,01899000111,,\"House 7, Road 3, City\",1990-05-01,,\n";

        $filename = 'customer_import_sample.csv';
        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimetypes:text/plain,text/csv,text/tsv', 'max:2048'],
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        [$rows, $headers, $errors] = $this->parseCsv($path);

        // Persist parsed rows in session for processing step
        Session::put('customer_import_rows', $rows);

        return view('admin.customers.import_preview', [
            'headers' => $headers,
            'rows' => $rows,
            'errors' => $errors,
        ]);
    }

    public function process(Request $request)
    {
        $rows = Session::get('customer_import_rows', []);
        if (empty($rows)) {
            return redirect()->route('admin.customers.import')->withErrors(['file' => 'No parsed data found. Please upload the file again.']);
        }

        $created = 0; $updated = 0; $skipped = 0; $rowNum = 1;
        foreach ($rows as $row) {
            $rowNum++;
            // Basic row validation
            $name = trim($row['name'] ?? '');
            $mobile = trim($row['mobile'] ?? ($row['phone'] ?? ''));
            $email = trim($row['email'] ?? '');
            if ($name === '' || ($mobile === '' && $email === '')) { $skipped++; continue; }

            $data = [
                'name' => $name,
                'mobile' => $mobile ?: null,
                'email' => $email ?: null,
                'address' => trim($row['address'] ?? '') ?: null,
                'dob' => trim($row['dob'] ?? '') ?: null,
                'delivery_class' => trim($row['delivery_class'] ?? '') ?: null,
                'kam' => isset($row['kam']) && is_numeric($row['kam']) ? (int) $row['kam'] : null,
            ];

            // Find existing by mobile first, otherwise by email
            $query = Customer::query();
            if ($mobile) { $query->where('mobile', $mobile); }
            elseif ($email) { $query->where('email', $email); }

            $existing = $query->first();
            if ($existing) {
                $existing->fill($data);
                $existing->save();
                $updated++;
            } else {
                Customer::create($data);
                $created++;
            }
        }

        Session::forget('customer_import_rows');

        return redirect()->route('admin.customers.index')
            ->with('success', "Import completed. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}.");
    }

    private function parseCsv(string $path): array
    {
        $rows = []; $headers = []; $errors = [];
        if (!is_readable($path)) { return [$rows, $headers, ['File could not be read']]; }

        if (($handle = fopen($path, 'r')) !== false) {
            $line = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $line++;
                // Skip empty lines
                if ($line === 1) {
                    $headers = array_map(function($h){ return strtolower(trim($h)); }, $data);
                    continue;
                }
                if (count(array_filter($data, fn($v) => trim((string)$v) !== '')) === 0) { continue; }

                $row = [];
                foreach ($headers as $i => $key) {
                    $row[$key] = $data[$i] ?? null;
                }

                // Normalize alternative header names
                if (!isset($row['mobile']) && isset($row['phone'])) { $row['mobile'] = $row['phone']; }

                // Light validation for preview
                if (empty(trim($row['name'] ?? ''))) {
                    $errors[] = "Line {$line}: Missing name";
                }
                if (empty(trim($row['mobile'] ?? '')) && empty(trim($row['email'] ?? ''))) {
                    $errors[] = "Line {$line}: Need mobile or email";
                }

                $rows[] = $row;
            }
            fclose($handle);
        }

        return [$rows, $headers, $errors];
    }
}
