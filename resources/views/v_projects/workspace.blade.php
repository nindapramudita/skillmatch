@extends('v_layouts.app')

@section('title', $project->title . ' - Kelola Projek')

@push('styles')
    @vite('resources/css/projects.css')
@endpush

@section('content')
@include('v_layouts.navbar')

@php
    $milestones = collect($project->milestones ?? []);
    $documents = collect($project->documents ?? []);
    $status = $project->status === 'ongoing'
        ? 'SEDANG BERJALAN'
        : 'SELESAI';

    $owner = $project->owner;

    $members = $project->members
        ->reject(fn ($member) => (int) $member->id === (int) $project->owner_id)
        ->values();

    $ownerPhoto = $owner?->profile_photo
        ? asset('storage/' . $owner->profile_photo)
        : asset('images/logo-avatar.png');
@endphp

<main class="workspace-page">
    <section class="workspace-shell">

        <header class="workspace-hero">
            <div class="workspace-hero-top">
                <a href="{{ route('projects', ['tab' => $isOwner ? 'created' : 'joined']) }}" class="workspace-back">
                    ← Projek Saya
                </a>

                <span class="workspace-label">
                    {{ $isOwner ? 'Kelola Projek' : 'Anggota Tim' }}
                </span>
            </div>

            <h1>{{ $project->title }}</h1>

            <p class="workspace-status {{ $project->status === 'completed' ? 'is-completed' : '' }}">
                STATUS: {{ $status }}
            </p>

            <div class="workspace-team" aria-label="Anggota tim projek">

                {{-- OWNER --}}
                <div class="workspace-member-card">
                    <div class="workspace-member-avatar-wrap">
                        <img
                            class="workspace-member-avatar"
                            src="{{ $ownerPhoto }}"
                            alt="{{ $owner?->name ?? 'Pemilik Projek' }}"
                        >
                    </div>

                    <strong>{{ $owner?->name ?? 'Pemilik Projek' }}</strong>
                    <small>Pemilik Projek</small>
                </div>

                {{-- MEMBER --}}
                @foreach($members as $member)
                    @php
                        $memberPhoto = $member->profile_photo
                            ? asset('storage/' . $member->profile_photo)
                            : asset('images/logo-avatar.png');

                        $memberRole = $member->pivot?->role ?: 'Anggota';
                    @endphp

                    <div class="workspace-member-card">
                        <div class="workspace-member-avatar-wrap">
                            <img
                                class="workspace-member-avatar"
                                src="{{ $memberPhoto }}"
                                alt="{{ $member->name }}"
                            >

                            @if($isOwner && $project->status === 'ongoing')
                                <button
                                    type="button"
                                    class="workspace-member-remove js-remove-member"
                                    data-form-id="remove-member-{{ $member->id }}"
                                    data-member-name="{{ $member->name }}"
                                    aria-label="Keluarkan {{ $member->name }} dari projek"
                                    title="Keluarkan anggota"
                                >
                                    ×
                                </button>

                                <form
                                    id="remove-member-{{ $member->id }}"
                                    method="POST"
                                    action="{{ route('projects.workspace.members.destroy', [$project, $member]) }}"
                                    hidden
                                >
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>

                        <strong>{{ $member->name }}</strong>
                        <small>{{ $memberRole }}</small>
                    </div>
                @endforeach

                @if($members->isEmpty())
                    <div class="workspace-member-placeholder">
                        Belum ada anggota lain di projek ini.
                    </div>
                @endif
            </div>
        </header>

        @unless($isOwner)
            <p class="workspace-feedback" role="status">
                Kamu sudah diterima sebagai anggota projek ini.
            </p>
        @endunless

        @if(session('success'))
            <p class="workspace-feedback" role="status">
                {{ session('success') }}
            </p>
        @endif

        @if($errors->any())
            <div class="workspace-feedback workspace-feedback-error" role="alert">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="workspace-grid">

            {{-- CAPAIAN --}}
            <section class="workspace-card" aria-labelledby="milestoneHeading">
                <h2 id="milestoneHeading">Capaian - TIM</h2>

                <div class="workspace-list">
                    @forelse($milestones as $index => $milestone)
                        @php
                            $label = is_array($milestone)
                                ? ($milestone['name'] ?? $milestone['title'] ?? '')
                                : $milestone;

                            $milestoneStatus = is_array($milestone)
                                ? ($milestone['status'] ?? '')
                                : '';

                            $complete = preg_match(
                                '/selesai|done|completed/i',
                                (string) $milestoneStatus
                            );
                        @endphp

                        @if(filled($label))
                            @if($isOwner && $project->status === 'ongoing')
                                <form
                                    method="POST"
                                    action="{{ route('projects.workspace.milestones.toggle', [$project, $index]) }}"
                                    class="workspace-check-form"
                                >
                                    @csrf

                                    <label class="workspace-check">
                                        <input
                                            type="checkbox"
                                            @checked($complete)
                                            onchange="this.form.submit()"
                                            aria-label="Tandai {{ $label }} selesai"
                                        >
                                        <span>{{ $label }}</span>
                                    </label>
                                </form>
                            @else
                                <span class="workspace-check">
                                    <input type="checkbox" disabled @checked($complete)>
                                    <span>{{ $label }}</span>
                                </span>
                            @endif
                        @endif
                    @empty
                        <p class="workspace-empty">Belum ada capaian.</p>
                    @endforelse
                </div>

                @if($isOwner && $project->status === 'ongoing')
                    <form
                        method="POST"
                        action="{{ route('projects.workspace.milestones.store', $project) }}"
                        class="workspace-inline-form"
                    >
                        @csrf

                        <label for="milestone-title" class="sr-only">
                            Judul capaian
                        </label>

                        <input
                            id="milestone-title"
                            name="title"
                            type="text"
                            maxlength="255"
                            required
                            value="{{ old('title') }}"
                            placeholder="Judul capaian"
                        >

                        <button type="submit">
                            Tambah Capaian
                        </button>
                    </form>
                @endif
            </section>

            {{-- FILE --}}
            <section class="workspace-card" aria-labelledby="documentsHeading">
                <h2 id="documentsHeading">File &amp; Dokumen</h2>

                <div class="workspace-list">
                    @forelse($documents as $document)
                        @php
                            $label = is_array($document)
                                ? ($document['name'] ?? $document['title'] ?? '')
                                : $document;

                            $path = is_array($document)
                                ? ($document['path'] ?? null)
                                : null;
                        @endphp

                        @if(filled($label))
                            @if($path)
                                <a
                                    class="workspace-document"
                                    href="{{ asset('storage/' . $path) }}"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    {{ $label }}
                                </a>
                            @else
                                <p class="workspace-document">
                                    {{ $label }}
                                </p>
                            @endif
                        @endif
                    @empty
                        <p class="workspace-empty">
                            Belum ada file atau dokumen.
                        </p>
                    @endforelse
                </div>

                @if($isOwner && $project->status === 'ongoing')
                    <form
                        method="POST"
                        action="{{ route('projects.workspace.documents.store', $project) }}"
                        enctype="multipart/form-data"
                        class="workspace-upload-form"
                    >
                        @csrf

                        <input
                            id="project-document"
                            name="document"
                            type="file"
                            class="sr-only"
                            required
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.png,.jpg,.jpeg,.webp"
                        >

                        <label for="project-document" class="workspace-file-picker">
                            Pilih File
                        </label>

                        <button type="submit">
                            Tambah File
                        </button>
                    </form>
                @endif
            </section>

            {{-- CHAT --}}
            <section class="workspace-card workspace-chat-card" aria-labelledby="teamChatHeading">
                <h2 id="teamChatHeading">Chat Tim</h2>

                <p>
                    Diskusikan progres projek bersama anggota tim.
                </p>

                <a
                    href="{{ route('projects.chat', $project) }}"
                    class="workspace-chat-btn"
                >
                    Buka Halaman Chat
                </a>
            </section>
        </div>

        <footer class="workspace-footer">
            <p>
                ◷ Tenggat Projek:
                <strong>
                    {{ $project->deadline?->translatedFormat('d F Y') ?? 'Belum ditentukan' }}
                </strong>
            </p>

            @if($isOwner)
                @if($project->status === 'ongoing')
                    <form
                        method="POST"
                        action="{{ route('projects.complete', $project) }}"
                        onsubmit="return confirm('Tandai projek ini sebagai selesai?');"
                    >
                        @csrf

                        <button type="submit" class="workspace-complete-btn">
                            Tandai Projek selesai
                        </button>
                    </form>
                @else
                    <span class="workspace-completed-label">
                        ✓ Projek telah selesai
                    </span>
                @endif
            @endif
        </footer>
    </section>
</main>

{{-- MODAL HAPUS ANGGOTA --}}
<div
    class="member-remove-modal"
    id="memberRemoveModal"
    hidden
    aria-hidden="true"
>
    <div class="member-remove-backdrop js-close-remove-modal"></div>

    <section
        class="member-remove-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="removeMemberTitle"
    >
        <div class="member-remove-warning">!</div>

        <h2 id="removeMemberTitle">
            Hapus Anggota Projek?
        </h2>

        <p>
            <span id="removeMemberName">Anggota ini</span>
            akan dihapus dari projek. Lanjutkan?
        </p>

        <div class="member-remove-actions">
            <button type="button" class="member-remove-cancel js-close-remove-modal">
                Batal
            </button>

            <button type="button" class="member-remove-confirm" id="confirmRemoveMember">
                Ya, Hapus
            </button>
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('memberRemoveModal');
    const confirmButton = document.getElementById('confirmRemoveMember');
    const memberName = document.getElementById('removeMemberName');
    const openButtons = document.querySelectorAll('.js-remove-member');
    const closeButtons = document.querySelectorAll('.js-close-remove-modal');

    let activeFormId = null;

    function openModal(button) {
        activeFormId = button.dataset.formId;
        memberName.textContent = button.dataset.memberName || 'Anggota ini';

        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
        activeFormId = null;
    }

    openButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            openModal(button);
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    confirmButton?.addEventListener('click', function () {
        if (!activeFormId) return;

        const form = document.getElementById(activeFormId);

        if (form) {
            form.submit();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });
});
</script>
@endpush
