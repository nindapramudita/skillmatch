<dialog class="join-project-modal" id="joinProjectModal">
    <div class="join-modal-header">
        <button
            type="button"
            class="join-modal-close"
            id="joinModalClose"
            aria-label="Tutup"
        >
            ×
        </button>

        <h2>KONFIRMASI GABUNG</h2>
    </div>

    <div class="join-modal-card">
        <h3 id="joinModalTitle">Nama Projek</h3>

        <p class="join-modal-description" id="joinModalDescription"></p>

        <div class="join-modal-owner">
            <img id="joinModalAvatar" src="" alt="Foto pemilik projek">
            <span id="joinModalOwner"></span>
        </div>

        <div class="join-modal-match-row">
            <span>Skill match</span>
            <strong id="joinModalMatchText">0%</strong>
        </div>

        <div class="join-modal-progress">
            <span id="joinModalProgressFill"></span>
        </div>

        <div class="join-modal-meta">
            <span>
                <i class="fa-solid fa-users"></i>
                <b id="joinModalMembers">0/0</b>
            </span>

            <span>
                <i class="fa-regular fa-calendar"></i>
                <b id="joinModalDeadline">-</b>
            </span>
        </div>
    </div>

    <div class="join-modal-actions">
        <button
            type="button"
            class="join-modal-cancel"
            id="joinModalCancel"
        >
            Batal
        </button>

        <form
            method="POST"
            action=""
            id="joinProjectForm"
        >
            @csrf

            <button
                type="submit"
                class="join-modal-submit"
            >
                Ya, Gabung
            </button>
        </form>
    </div>
</dialog>

<style>
    .join-project-modal {
        width: min(415px, calc(100vw - 32px));
        max-width: none;
        margin: auto;
        padding: 24px 26px 28px;
        border: 1px solid rgba(210, 240, 248, .9);
        border-radius: 24px;
        background: linear-gradient(145deg, rgba(50, 103, 122, .97), rgba(17, 59, 78, .98));
        color: #fff;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .38);
    }

    .join-project-modal::backdrop {
        background: rgba(0, 18, 29, .72);
        backdrop-filter: blur(2px);
    }

    .join-modal-header {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 16px;
        min-height: 38px;
    }

    .join-modal-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 800;
    }

    .join-modal-close {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 34px;
        height: 34px;
        padding: 0;
        border: 2px solid #e8f8fc;
        border-radius: 50%;
        background: transparent;
        color: #fff;
        font-size: 27px;
        line-height: 27px;
        cursor: pointer;
    }

    .join-modal-card {
        padding: 18px 18px 16px;
        border: 1px solid #a8bc37;
        border-radius: 26px;
        background: rgba(255, 255, 255, .07);
    }

    .join-modal-card h3 {
        margin: 0 0 16px;
        font-size: 17px;
        color: #fff;
    }

    .join-modal-description {
        min-height: 42px;
        margin: 0 0 18px;
        color: #e5f0f4;
        font-size: 13px;
        line-height: 1.55;
    }

    .join-modal-owner {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .join-modal-owner img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
    }

    .join-modal-owner span {
        font-size: 13px;
        font-weight: 700;
    }

    .join-modal-match-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .join-modal-progress {
        width: 100%;
        height: 13px;
        overflow: hidden;
        border-radius: 999px;
        background: #082d40;
    }

    .join-modal-progress span {
        display: block;
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: #b0edf9;
        transition: width .2s ease;
    }

    .join-modal-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-top: 17px;
        font-size: 13px;
    }

    .join-modal-meta span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .join-modal-meta i {
        color: #e6f8fc;
        font-size: 20px;
    }

    .join-modal-meta b {
        font-weight: 500;
    }

    .join-modal-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 36px;
        margin-top: 22px;
        padding: 0 14px;
    }

    .join-modal-actions form {
        margin: 0;
    }

    .join-modal-cancel,
    .join-modal-submit {
        width: 100%;
        min-height: 43px;
        border-radius: 8px;
        font: inherit;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
    }

    .join-modal-cancel {
        border: 1px solid #b0edf9;
        background: #063750;
        color: #fff;
    }

    .join-modal-submit {
        border: 0;
        background: #b0edf9;
        color: #07374c;
    }

    @media (max-width: 520px) {
        .join-project-modal {
            padding: 20px 16px 22px;
        }

        .join-modal-actions {
            gap: 12px;
            padding: 0;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('joinProjectModal');

    if (!modal) return;

    const form = document.getElementById('joinProjectForm');
    const title = document.getElementById('joinModalTitle');
    const description = document.getElementById('joinModalDescription');
    const owner = document.getElementById('joinModalOwner');
    const avatar = document.getElementById('joinModalAvatar');
    const matchText = document.getElementById('joinModalMatchText');
    const progressFill = document.getElementById('joinModalProgressFill');
    const members = document.getElementById('joinModalMembers');
    const deadline = document.getElementById('joinModalDeadline');

    function closeModal() {
        modal.close();
    }

    document.querySelectorAll('.js-open-join-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            const match = Math.max(0, Math.min(100, Number(button.dataset.match || 0)));

            title.textContent = button.dataset.title || 'Projek';
            description.textContent = button.dataset.description || '';
            owner.textContent = button.dataset.owner || 'Pengguna';
            avatar.src = button.dataset.avatar || '';
            matchText.textContent = match + '%';
            progressFill.style.width = match + '%';
            members.textContent = button.dataset.members || '-';
            deadline.textContent = button.dataset.deadline || '-';
            form.action = button.dataset.joinUrl || '';

            modal.showModal();
        });
    });

    document.getElementById('joinModalClose')?.addEventListener('click', closeModal);
    document.getElementById('joinModalCancel')?.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>
