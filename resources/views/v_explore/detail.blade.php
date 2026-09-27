@extends('v_layouts.app')

@section('title', $project->title . ' - SkillMatch')

@push('styles')
    @vite('resources/css/detail.css')
@endpush

@section('content')
@include('v_layouts.navbar')
@php
    $roleNames = collect($project->roles ?? [])->pluck('name')->filter()->values();
    $userSkills = collect(auth()->user()?->skills ?? [])->filter()->map(fn ($skill) => strtolower(trim($skill)));
    $matchPercentage = $roleNames->isNotEmpty() && $userSkills->isNotEmpty()
        ? (int) round($roleNames->filter(fn ($role) => $userSkills->contains(strtolower(trim($role))))->count() / $roleNames->count() * 100)
        : 0;
    $isOwner = auth()->id() === $project->owner_id;
    $isMember = auth()->check() && $project->members->contains(auth()->id());
@endphp

<main class="detail-page-wrapper">
    <div class="detail-card">
        
        {{-- Header: Judul & Tenggat --}}
        <div class="detail-card-header">
            <h1 class="ai-deteksi-sampah">{{ $project->title }}</h1>
            <div class="tenggat-projek-11-agustus-202-parent">
                <i class="fa-regular fa-calendar group-icon"></i>
                <span class="tenggat-projek-11">Tenggat Projek: {{ $project->deadline?->locale('id')->translatedFormat('d F Y') ?? 'Belum ditentukan' }}</span>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="detail-grid">
            
            {{-- Kolom Kiri --}}
            <div class="detail-left-col">
                <div class="owner-target-wrapper">
                    <div class="group-inner-box">
                        <div class="owner-avatar">
                            <img class="rectangle-icon" src="{{ $project->owner?->profile_photo ? asset('storage/'.$project->owner->profile_photo) : asset('images/logo-avatar.png') }}" alt="{{ $project->owner?->name ?? 'Pengguna' }}">
                        </div>
                        <div class="tharishah-nur-fadhilah">{{ $project->owner?->name ?? 'Pengguna' }}</div>
                    </div>

                    <div class="rencana-capaian-projek-container">
                        <span class="rencana-capaian-projek-container2">
                            <p class="rencana-capaian-projek">Rencana Capaian Projek :</p>
                            <ul class="reset-dataset-sampah-integraf">
                                @forelse($project->milestones ?? [] as $milestone)
                                    <li class="reset-dataset-sampah">{{ is_array($milestone) ? ($milestone['title'] ?? $milestone['name'] ?? '') : $milestone }}</li>
                                @empty
                                    <li>Belum ada capaian.</li>
                                @endforelse
                            </ul>
                        </span>
                    </div>
                </div>

                <div class="mengembangkan-sistem-deteksi-c-parent">
                    <b class="detail-projek">Detail Projek :</b>
                    <div class="mengembangkan-sistem-deteksi">
                        {{ $project->description }}
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan --}}
            <div class="detail-right-col">
                <div class="group-wrapper">
                    <div class="rectangle-container">
                        <b class="kebutuhan-skill">Kebutuhan Skill</b>
                        <div class="skills-badges">
                            @forelse($roleNames as $role)
                                <b class="skill-tag">{{ $role }}</b>
                            @empty
                                <span>Belum ditentukan</span>
                            @endforelse
                        </div>
                        
                        <div class="skill-match-wrapper">
                            <div class="skill-match">Skill match</div>
                            <div class="div2">{{ $matchPercentage }}%</div>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $matchPercentage }}%;"></div>
                        </div>
                    </div>
                </div>

                <div class="roles-needed-wrapper">
                    <b class="role-yang-dibutuhkan">Role Yang Dibutuhkan:</b>
                    <div class="roles-avatars-list">
                        @forelse($roleNames as $role)
                            <div class="role-item">
                                <div class="group-child3-plus"><div class="div">+</div></div>
                                <div class="frontend-developer">{{ $role }}</div>
                            </div>
                        @empty
                            <span>Belum ada role yang ditentukan.</span>
                        @endforelse
                    </div>
                </div>

                @php
                    $ownerPhoto = $project->owner?->profile_photo
                        ? asset('storage/' . $project->owner->profile_photo)
                        : asset('images/logo-avatar.png');

                    $targetMembers = max(
                        $project->members->count(),
                        $roleNames->count() + 1,
                        2
                    );
                @endphp

                <div class="action-buttons-wrapper">
                    <a href="{{ route('explore') }}" class="group-child11 kembali">Kembali</a>

                    @if($isOwner || $isMember || $application?->status === 'accepted')
                        <a href="{{ route('projects.workspace', $project) }}" class="group-child12 ya-gabung">Kelola Projek</a>
                    @elseif($application?->status === 'pending')
                        <a href="{{ route('projects', ['tab' => 'joined']) }}" class="group-child12 ya-gabung">Menunggu Persetujuan</a>
                    @elseif($application?->status === 'rejected')
                        <button type="button" class="group-child12 ya-gabung" disabled>Permintaan Ditolak</button>
                    @elseif(auth()->check() && $project->status === 'ongoing')
                        <button
                            type="button"
                            class="group-child12 ya-gabung js-open-join-modal"
                            data-title="{{ $project->title }}"
                            data-description="{{ $project->description }}"
                            data-owner="{{ $project->owner?->name ?? 'Pengguna' }}"
                            data-avatar="{{ $ownerPhoto }}"
                            data-match="{{ $matchPercentage }}"
                            data-members="{{ $project->members->count() }}/{{ $targetMembers }}"
                            data-deadline="{{ $project->deadline?->locale('id')->translatedFormat('d F Y') ?? 'Belum ditentukan' }}"
                            data-join-url="{{ route('projects.join', $project) }}"
                        >
                            Ya, Gabung
                        </button>
                    @elseif(!auth()->check() && $project->status === 'ongoing')
                        <a href="{{ route('login') }}" class="group-child12 ya-gabung">Ya, Gabung</a>
                    @endif
                </div>

                @error('join')
                    <p role="alert">{{ $message }}</p>
                @enderror
            </div>

        </div>

    </div>
</main>

@include('v_explore.join-modal')
@endsection
