<style>
.question-scroll {
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 6px;
}

.question-scroll::-webkit-scrollbar {
    width: 6px;
}

.question-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}
</style>

<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Soal Ujian: <?= $exam->exam_name ?></h5>
        <div class="d-flex gap-2">
            <a href="<?= base_url('exam/management/' . $exam->class_id); ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#bulkQuestionModal">
                <i class="bi bi-plus-lg"></i> Tambah Soal
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="questionTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Pertanyaan</th>
                        <th width="10%">Kunci</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="bulkQuestionModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <form id="bulkForm">
                <input type="hidden" name="exam_id" value="<?= $exam->exam_id ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Soal Sekaligus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body bg-light">
                    <!-- SCROLL CONTAINER -->
                    <div id="questionsContainer" class="question-scroll mb-3"></div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-primary border-dashed" id="btnAddRow">
                            <i class="bi bi-plus-circle"></i> Tambah Soal Baru
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Semua Soal
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<template id="questionRowTemplate">
    <div class="card mb-3 question-row shadow-sm border-0">

        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <span class="fw-bold text-primary">
                Soal #<span class="row-number"></span>
            </span>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </div>

        <div class="card-body">

            <div class="mb-3">
                <label class="form-label fw-semibold">Pertanyaan</label>
                <textarea name="question[]" class="form-control" rows="2" placeholder="Tulis pertanyaan..."
                    required></textarea>
            </div>

            <div class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">A</span>
                        <input type="text" name="option_a[]" class="form-control" placeholder="Pilihan A" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">B</span>
                        <input type="text" name="option_b[]" class="form-control" placeholder="Pilihan B" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">C</span>
                        <input type="text" name="option_c[]" class="form-control" placeholder="Pilihan C" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text fw-bold">D</span>
                        <input type="text" name="option_d[]" class="form-control" placeholder="Pilihan D" required>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex align-items-center gap-2">
                <label class="fw-bold mb-0">Kunci Jawaban:</label>
                <select name="correct_answer[]" class="form-select form-select-sm w-auto" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

        </div>
    </div>
</template>


<div class="modal fade" id="editQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editQuestionForm">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="exam_id" value="<?= $exam->exam_id ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                    value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pertanyaan</label>
                        <textarea name="question" id="edit_question" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small">Opsi A</label>
                            <input type="text" name="option_a" id="edit_option_a" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Opsi B</label>
                            <input type="text" name="option_b" id="edit_option_b" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Opsi C</label>
                            <input type="text" name="option_c" id="edit_option_c" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Opsi D</label>
                            <input type="text" name="option_d" id="edit_option_d" class="form-control form-control-sm"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kunci Jawaban</label>
                        <select name="correct_answer" id="edit_correct_answer" class="form-select" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variabel global untuk ID Exam
window.EXAM_ID = "<?= $exam->exam_id ?>";
window.BASE_URL = "<?= base_url() ?>";
window.CSRF_TOKEN_NAME = "<?= $this->security->get_csrf_token_name() ?>";
</script>
<script type="module" src="<?= base_url('assets/js/question_modules.js') ?>"></script>