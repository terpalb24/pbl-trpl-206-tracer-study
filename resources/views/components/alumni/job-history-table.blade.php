@if($jobHistories->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">NO</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">POSISI</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">NAMA PERUSAHAAN</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-600 uppercase text-sm">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobHistories as $index => $jobHistory)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">{{ $index + 1 }}</td>
                    <td class="py-3 px-4 font-medium">{{ $jobHistory->position }}</td>
                    <td class="py-3 px-4">
                        {{ $jobHistory->company->company_name ?? '-' }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('alumni.job-history.edit', $jobHistory->id_jobhistory) }}"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-yellow-200 hover:bg-yellow-300 text-yellow-700 transition"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('alumni.job-history.destroy', $jobHistory->id_jobhistory) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat kerja ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-200 hover:bg-red-300 text-red-700 transition"
                                    title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>

                            <button type="button"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-200 hover:bg-blue-300 text-blue-700 transition"
                                title="Detail"
                                onclick="showDetail({{ $jobHistory->id_jobhistory }})">
                                <i class="fas fa-info-circle"></i>
                            </button>
                        </div>

                        {{-- Modal Detail --}}
                        <div id="modal-detail-{{ $jobHistory->id_jobhistory }}" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/10 hidden">
                            <div class="bg-white rounded-lg shadow-lg p-10 w-full max-w-2xl relative">
                                <button onclick="closeDetail({{ $jobHistory->id_jobhistory }})" class="absolute top-2 right-2 text-gray-500 hover:text-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                                <h2 class="text-2xl font-semibold mb-4 text-blue-800">Detail Riwayat Kerja</h2>
                                <div class="mb-4"><strong>Nama Perusahaan:</strong> {{ $jobHistory->company->company_name ?? '-' }}</div>
                                <div class="mb-4"><strong>Posisi:</strong> {{ $jobHistory->position }}</div>
                                <div class="mb-4"><strong>Gaji:</strong> Rp {{ $jobHistory->salary }}</div>
                                <div class="mb-4"><strong>Durasi:</strong> {{ $jobHistory->duration }}</div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
     <div class="flex justify-end mb-4 mt-4 ">
        <a href="{{ route('alumni.job-history.create') }}"
           class="bg-blue-900 hover:bg-blue-900 text-white font-semibold py-2 px-4 rounded transition duration-300">
        + Tambah Riwayat Kerja
        </a>
        </div>
@else
    <x-alumni.job-history-empty />
@endif
