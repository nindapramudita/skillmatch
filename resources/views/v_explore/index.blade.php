@extends('v_layouts.app')

@section('title', 'Rekomendasi Projek - SkillMatch')

@push('styles')
    @vite('resources/css/explore.css')

    <style>
        .explore-page {
            background:
                linear-gradient(
                    90deg,
                    rgba(4, 52, 76, 0.72) 0%,
                    rgba(4, 52, 76, 0.48) 50%,
                    rgba(4, 52, 76, 0.68) 100%
                ),
                url("{{ asset('images/bg.png') }}") center center / cover no-repeat fixed !important;
        }
    </style>
@endpush

@section('content')
@include('v_layouts.navbar')

<main class="explore-page">
    <section class="explore-hero">
        <p class="explore-eyebrow">Projek aktif untuk skillmu</p>

        <h1>
            Temukan Projek<br>
            <span>yang Membutuhkan</span> Skillmu
        </h1>

        <p>
            Jelajahi projek aktif berdasarkan peran yang sesuai dengan skill di profilmu.
        </p>

        <form
            class="explore-search"
            method="GET"
            action="{{ route('explore') }}"
        >
            <input
                name="q"
                value="{{ $search }}"
                placeholder="Cari projek, peran, atau pemilik"
                aria-label="Cari projek atau peran"
            >

            <button type="submit" aria-label="Cari">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
    </section>

    @if(session('success'))
        <div class="project-success" style="max-width:1200px;margin:22px auto 0;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="project-success" style="max-width:1200px;margin:22px auto 0;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="explore-results" aria-labelledby="result-title">
        <div class="section-heading">
            <div>
                <p class="explore-eyebrow">Berdasarkan skill profilmu</p>
                <h2 id="result-title">Rekomendasi Untukmu</h2>
            </div>

            <span>{{ $projects->count() }} projek tersedia</span>
        </div>

        <div class="member-grid project-grid">
            @forelse ($projects as $project)
                @php
                    $roleNames = collect($project->roles ?? [])
                        ->pluck('name')
                        ->filter(fn ($role) => is_string($role) && trim($role) !== '')
                        ->values();

                    $userSkills = collect(auth()->user()?->skills ?? [])
                        ->filter(fn ($skill) => is_string($skill) && trim($skill) !== '')
                        ->map(fn ($skill) => strtolower(trim($skill)))
                        ->unique()
                        ->values();

                    $normalizedRoles = $roleNames
                        ->map(fn ($role) => strtolower(trim($role)))
                        ->unique()
                        ->values();

                    $matchCount = $normalizedRoles->intersect($userSkills)->count();
                    $matchPercentage = $normalizedRoles->count() > 0
                        ? (int) round(($matchCount / $normalizedRoles->count()) * 100)
                        : 0;

                    $application = $applications->get($project->id);
                    $isOwner = auth()->check() && (int) auth()->id() === (int) $project->owner_id;
                    $isMember = auth()->check() && $project->members->contains(auth()->id());

                    $ownerPhoto = $project->owner?->profile_photo
                        ? asset('storage/' . $project->owner->profile_photo)
                        : asset('images/logo-avatar.png');

                    $targetMembers = max(
                        $project->members->count(),
                        $roleNames->count() + 1,
                        2
                    );
                @endphp

                <article class="member-card project-card">
                    <div class="member-card-top">
                        <span class="availability">
                            <i></i>
                            Dibuka
                        </span>

                        <div class="skill-tags">
                            @forelse($roleNames->take(3) as $role)
                                <span>{{ $role }}</span>
                            @empty
                                <span>Umum</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="member-identity">
                        <div>
                            <h3>{{ $project->title }}</h3>
                            <p class="member-university">
                                {{ \Illuminate\Support\Str::limit($project->description, 105) }}
                            </p>
                        </div>
                    </div>

                    <div class="project-owner">
                        <img
                            src="{{ $ownerPhoto }}"
                            alt="{{ $project->owner?->name ?? 'Pemilik Projek' }}"
                            class="owner-avatar"
                        >

                        <span>{{ $project->owner?->name ?? 'Pengguna' }}</span>
                    </div>

                    <div class="project-progress-wrapper">
                        <div class="project-progress-label">
                            <span>Skill match</span>
                            <strong class="match-text">{{ $matchPercentage }}%</strong>
                        </div>

                        <progress
                            class="project-progress match-bar"
                            value="{{ $matchPercentage }}"
                            max="100"
                        ></progress>
                    </div>

                    <div class="project-meta">
                        <span class="meta-members">
                            <i class="fa-solid fa-users"></i>
                            {{ $project->members->count() }}/{{ $targetMembers }}
                        </span>

                        <span class="meta-deadline">
                            <i class="fa-regular fa-calendar"></i>
                            {{ $project->deadline?->locale('id')->translatedFormat('d F Y') ?? 'Belum ditentukan' }}
                        </span>
                    </div>

                    <div class="member-actions">
                        <a
                            class="outline-button"
                            href="{{ route('explore.detail', $project) }}"
                        >
                            Lihat Detail
                        </a>

                        @guest
                            <a
                                class="primary-button"
                                href="{{ route('login') }}"
                            >
                                Gabung
                            </a>
                        @else
                            @if($isOwner)
                                <button class="primary-button" type="button" disabled>
                                    Projek Milikmu
                                </button>
                            @elseif($isMember || $application?->status === 'accepted')
                                <a
                                    class="primary-button"
                                    href="{{ route('projects.workspace', $project) }}"
                                >
                                    Buka Projek
                                </a>
                            @elseif($application?->status === 'pending')
                                <a
                                    class="primary-button"
                                    href="{{ route('projects', ['tab' => 'joined']) }}"
                                >
                                    Menunggu
                                </a>
                            @elseif($application?->status === 'rejected')
                                <button class="primary-button" type="button" disabled>
                                    Ditolak
                                </button>
                            @else
                                <button
                                    class="primary-button js-open-join-modal"
                                    type="button"
                                    data-title="{{ $project->title }}"
                                    data-description="{{ $project->description }}"
                                    data-owner="{{ $project->owner?->name ?? 'Pengguna' }}"
                                    data-avatar="{{ $ownerPhoto }}"
                                    data-match="{{ $matchPercentage }}"
                                    data-members="{{ $project->members->count() }}/{{ $targetMembers }}"
                                    data-deadline="{{ $project->deadline?->locale('id')->translatedFormat('d F Y') ?? 'Belum ditentukan' }}"
                                    data-join-url="{{ route('projects.join', $project) }}"
                                >
                                    Gabung
                                </button>
                            @endif
                        @endguest
                    </div>
                </article>
            @empty
                <div class="member-card" style="grid-column:1/-1;text-align:center;">
                    <h3 style="color:white;">Belum ada projek aktif</h3>
                    <p class="member-university">
                        Projek aktif akan tampil di halaman ini.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
</main>

@include('v_explore.join-modal')
@endsection
