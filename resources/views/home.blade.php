<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="antialiased">
    <div
        class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 selection:bg-red-500 selection:text-white">
        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".csv" />
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                        Upload
                    </button>
                </form>
            </div>
            <div class="mt-4">
                <table>
                    <thead>
                        <tr>
                            <th class="p-2">Uploaded At</th>
                            <th class="p-2">File Path</th>
                            <th class="p-2">Status</th>
                        </tr>
                    <tbody>
                        @foreach ($uploads as $upload)
                            <tr>
                                <td class="p-2">
                                    {{ $upload->uploaded_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="p-2">
                                    <a href="{{ '/storage/' . $upload->path }}">{{ $upload->path }}</a>
                                </td>
                                <td class="p-2">
                                    {{ $upload->status }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

@if (\Session::has('success'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '{{ \Session::get('success') }}',
            })
        });
    </script>
@endif

@if (\Session::has('error'))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: '{{ \Session::get('error') }}',
            })
        });
    </script>
@endif

</html>
