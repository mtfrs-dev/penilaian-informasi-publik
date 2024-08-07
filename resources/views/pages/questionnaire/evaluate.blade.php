<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Penilaian Anugerah Keterbukaan Informasi Publik') }}</title>
    <link rel="icon" href="{{ asset('logo/KEMENHUB64.png') }}" type="image/png" sizes="32x32 16x16">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @if (isset($styles))
        {{ $styles }}
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body
    x-data="{
        showAdminEndExamPopUp : false,
        showEndExamPopUp : false,
        showExitPopUp : false,
        showSidebar : false,
        showSetJuryModal : {{ $errors->hasbag('set_jury') ? 'true' : 'false' }} 
    }">

    <!-- PAGE HEADER (EXAM TITLE) -->
    <nav class="fixed z-[999] top-0 w-full bg-primary text-white h-[3.5rem] flex justify-between xl:grid xl:grid-cols-3 items-center px-4">
        <div class="relative">
            <button type="button" x-on:click="showExitPopUp = true" class="block xl:flex xl:items-center xl:gap-2 text-white hover:text-gray-200">
                <span class="block m-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                </span>
                <span class="hidden text-xs xl:text-sm xl:block xl:m-0">Kembali</span>
            </button>
        </div>
        <div class="flex justify-center items-center gap-2">
            <img src="{{ asset('logo/KEMENHUB.png') }}" class="h-8 w-auto" alt="">
            <p class="block m-0 text-xs xl:text-sm xl:text-center">PENILAIAN ANUGERAH<br class="md:hidden"> KETERBUKAAN INFORMASI PUBLIK</p>
        </div>
    </nav>

    <!-- QUESTIONS NAVIGATION FOR MOBILE -->
    <div class="flex xl:hidden fixed z-[1010] top-[3.5rem] h-[3rem] shadow shadow-gray-400 w-full bg-primary-10 text-primary py-2 px-4 justify-between items-center"
        x-data="{showRespondentDetails: false}">
        @php
            $scored_count = 0;
            $total_score = 0;
            foreach ($indicators as $indicator => $categories) {
                foreach ($categories as $category => $questions) {
                    foreach ($questions as $question) {
                        $question->updated_by && $scored_count++;
                        $total_score += $question->score;
                    }
                }
            }
            $questionnaire_percentage = round(($scored_count/35)*100, 0);

            if ($questionnaire_percentage==100) {
                $questionnaire_percentage_classlist = [ "category-progress-container-100", "category-progress-100" ];
            } elseif ($questionnaire_percentage>=66) {
                $questionnaire_percentage_classlist = [ "category-progress-container-66", "category-progress-66" ];
            } elseif ($questionnaire_percentage>=33) {
                $questionnaire_percentage_classlist = [ "category-progress-container-33", "category-progress-33" ];
            } else {
                $questionnaire_percentage_classlist = [ "category-progress-container-default", "category-progress-default" ];
            }
        @endphp
        <div class="w-full h-full flex py-1 items-center justify-between relative">
            <span class="w-7 cursor-pointer" x-on:click="showRespondentDetails =! showRespondentDetails">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </span>
            <div id="progress_questionnaire_container" class="w-[calc(73%-1.75rem)] md:w-[calc(87%-1.75rem)] border {{ $questionnaire_percentage_classlist[0] }} rounded-lg h-full box-border overflow-hidden">
                <div id="progress_questionnaire" class="h-full border {{ $questionnaire_percentage_classlist[1] }} rounded-lg box-border overflow-hidden" style="width: {{ $questionnaire_percentage }}%;"></div>
            </div>
            <p class="w-[26%] md:w-[12%] text-sm font-bold text-right"><span id="questionnaire_scored_count">{{ $scored_count }}</span>/<span id="questionnaire_all_count">35</span> DINILAI</p>
            <div x-cloak x-show="showRespondentDetails" class="absolute w-full bg-primary-10 border-x border-primary-10/70 shadow shadow-gray-400 text-primary top-full mt-2 rounded-b-md p-2">
                <p class="font-semibold mb-2">Detail Responden</p>
                <div class="p-2 border border-primary-20 rounded-md bg-white text-primary-70 shadow-inner">
                    <table class="text-xs">
                        <tbody>
                            <tr class="">
                                <th class="py-1 w-[24%] align-top text-left">Nama</th>
                                <td class="py-1 inline-block mx-1">:</td>
                                <td class="py-1 text-left">{{ $respondent->name }}</td>
                            </tr>
                            <tr class="">
                                <th class="py-1 w-[24%] align-top text-left">Unit Kerja</th>
                                <td class="py-1 inline-block mx-1">:</td>
                                <td class="py-1 text-left">{{ $respondent->work_unit->name }}</td>
                            </tr>
                            <tr class="">
                                <th class="py-1 w-[24%] align-top text-left">Total Nilai</th>
                                <td class="py-1 inline-block mx-1">:</td>
                                <td class="questionnaire_total_score py-1 text-left">{{ $total_score }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <main class="relative w-full">
        <!-- QUESTIONS NAVIGATION -->
        <div class="hidden xl:block fixed z-[999] top-[3.5rem] h-[calc(100vh-3.5rem)] w-1/4 box-border border-r-4 border-primary bg-primary-10 bg-opacity-60 p-4">

            <div class="w-full m-0 mb-4 text-primary">
                <p class="font-semibold mb-2">Detail Responden</p>
                <div class="p-2 border border-primary-20 rounded-md bg-primary-10 text-primary-70 shadow-inner">
                    <table class="text-sm">
                        <tbody>
                            <tr class="">
                                <th class="py-1 w-[24%] align-top text-left">Nama</th>
                                <td class="py-1 inline-block mx-1">:</td>
                                <td class="py-1 text-left">{{ $respondent->name }}</td>
                            </tr>
                            <tr class="">
                                <th class="py-1 w-[24%] align-top text-left">Unit Kerja</th>
                                <td class="py-1 inline-block mx-1">:</td>
                                <td class="py-1 text-left">{{ $respondent->work_unit->name }}</td>
                            </tr>
                            <tr class="">
                                <th class="py-1 w-[24%] align-top text-left">Total Nilai</th>
                                <td class="py-1 inline-block mx-1">:</td>
                                <td class="questionnaire_total_score py-1 text-left">{{ $total_score }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-1 w-full items-center m-0 mb-2 font-semibold text-primary">
                <p class="text-base">Tanggapan Penilaian</p>
                <span id="saving" class="saving hidden text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 animate-spin">
                        <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0 1 12.548-3.364l1.903 1.903h-3.183a.75.75 0 1 0 0 1.5h4.992a.75.75 0 0 0 .75-.75V4.356a.75.75 0 0 0-1.5 0v3.18l-1.9-1.9A9 9 0 0 0 3.306 9.67a.75.75 0 1 0 1.45.388Zm15.408 3.352a.75.75 0 0 0-.919.53 7.5 7.5 0 0 1-12.548 3.364l-1.902-1.903h3.183a.75.75 0 0 0 0-1.5H2.984a.75.75 0 0 0-.75.75v4.992a.75.75 0 0 0 1.5 0v-3.18l1.9 1.9a9 9 0 0 0 15.059-4.035.75.75 0 0 0-.53-.918Z" clip-rule="evenodd" />
                    </svg>
                </span>
                <span id="saved" class="saved text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M17.7427 10.2859C17.7427 10.578 17.7184 10.8643 17.6716 11.1431H18.5999C20.7301 11.1431 22.457 12.87 22.457 15.0002C22.457 17.1305 20.7301 18.8574 18.5999 18.8574L12.5999 18.8574H5.74275C3.37581 18.8574 1.45703 16.9386 1.45703 14.5716C1.45703 12.2047 3.37581 10.2859 5.74275 10.2859H7.45703C7.45703 7.4456 9.75957 5.14307 12.5999 5.14307C15.4402 5.14307 17.7427 7.4456 17.7427 10.2859ZM16.08 11.8088L12.298 15.5908L12.298 15.5908L11.0374 16.8515L7.88569 13.6998L9.14636 12.4392L11.0373 14.3301L14.8193 10.5481L16.08 11.8088Z"/>
                    </svg>
                </span>
            </div>
            <div class="relative w-full h-fit max-h-[calc(100vh-17.5rem)] p-2 rounded-md bg-primary-10 overflow-y-auto custom-scrollbar border border-primary-20 shadow-inner">
                <div class="w-full h-fit" x-data="{
                        @for ($i = 0; $i < $indicators->count(); $i++)
                            showIndicator_{{ $i }}: {{ $i==0 ? 'true' : 'false' }},
                        @endfor
                    }">
                    @php
                        $upper_alphabet = "ABCDEGHIJKLMNOPQRSTUVWXYZ";
                        $lower_alphabet = "abcdefghijklmnopqrstuvwxyz";
                        $i=0;
                    @endphp
                    @foreach ($indicators as $indicator => $categories)
                        @php
                            $all_count          = 0;
                            $scored_count       = 0;
                            $indicator_score    = 0;
                            foreach ($categories as $category => $questions) {
                                $all_count      += $questions->count();
                                foreach ($questions as $question) {
                                    $question->updated_by && $scored_count++;
                                    $indicator_score += $question->score;
                                }
                            }
                            $indicator_percentage = round(($scored_count/$all_count)*100, 0);
                            if ($indicator_percentage==100) {
                                $indicator_percentage_classlist = "indicator-progess-100";
                            } elseif ($indicator_percentage>=66) {
                                $indicator_percentage_classlist = "indicator-progess-66";
                            } elseif ($indicator_percentage>=33) {
                                $indicator_percentage_classlist = "indicator-progess-33";
                            } else {
                                $indicator_percentage_classlist = "indicator-progess-default";
                            }
                        @endphp
                        <!-- NOMOR INDIKATOR -->
                        <button type="button" x-on:click="showIndicator_{{ $loop->index }} =! showIndicator_{{ $loop->index }}" 
                            class="flex justify-between items-center w-full box-border font-mono font-bold text-base p-1 lg:p-1.5 pr-2 bg-primary rounded-md text-white border {{ $loop->index === 0 ? '' : 'mt-2 lg:mt-3' }}">
                            <div class="flex gap-2 items-center">
                                <span id="progress_indikator_{{ $loop->index }}" class="w-12 {{ $indicator_percentage_classlist }} text-xs text-center rounded-md h-fit">{{ $indicator_percentage }}%</span>
                                <span>{{ $indicator }}</span>
                                <span id="indicator_total_score_{{ $loop->index }}" class="font-sans font-medium text-warning">({{ $indicator_score }})</span>
                            </div>
                            <span :class="showIndicator_{{ $loop->index }} && 'rotate-90'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                                    <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <div x-cloak x-show="showIndicator_{{ $loop->index }}" class="w-full box-border">
                            @php $j=0; @endphp
                            @foreach ($categories as $category => $questions)
                                @php
                                    $scored_questions_count   = 0;
                                    foreach ($questions as $question) {
                                        $question->updated_by && $scored_questions_count++;
                                    }
                                    $percentage = round(($scored_questions_count/$questions->count())*100, 0);
                                    if ($percentage==100) {
                                        $category_percentage_classlist = [ "category-progress-container-100", "category-progress-100" ];
                                    } elseif ($percentage>=66) {
                                        $category_percentage_classlist = [ "category-progress-container-66", "category-progress-66" ];
                                    } elseif ($percentage>=33) {
                                        $category_percentage_classlist = [ "category-progress-container-33", "category-progress-33" ];
                                    } else {
                                        $category_percentage_classlist = [ "category-progress-container-default", "category-progress-default" ];
                                    }
                                @endphp
                                <!-- KATEGORI -->
                                <button id="category-button_{{$i}}_{{$j}}" type="button" class="{{ $i==0 && $j==0 ? 'active-category-button' : '' }} cbtn border border-white block rounded-md hover:bg-gray-100 w-full p-1 lg:p-2 {{ $loop->index === 0 ? 'mt-1.5' : 'mt-2' }} border category-button">
                                    <!-- JUDUL KATEGORI -->
                                    <p class="text-left font-bold text-xs text-primary mb-1"> {{ $upper_alphabet[$loop->index] .". ". $category }} (<span class="category-total-score_{{$i}}_{{$j}} font-medium">{{ $questions->sum('score') }}</span>) </p>
                                    <!-- PROGRES KATEGORI -->
                                    <div class="flex justify-between w-full h-5">
                                        <div id="progres_category_container_{{$i}}_{{$j}}" class="w-[87%] border {{ $category_percentage_classlist[0] }} rounded-lg h-full box-border overflow-hidden">
                                            <div id="progres_category_{{$i}}_{{$j}}" class="h-full border {{ $category_percentage_classlist[1] }} rounded-lg box-border overflow-hidden" style="width: {{ $percentage }}%;"></div>
                                        </div>
                                        <p class="block w-[12%] text-sm text-right text-primary-60 font-mono">
                                            <span id="category_scored_count_{{$i}}_{{$j}}">{{ $scored_questions_count }}</span>/{{ $questions->count() }}
                                        </p>
                                    </div>
                                </button>
                                @php $j++; @endphp
                            @endforeach
                            @php $i++; @endphp
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- QUESTION CONTAINER -->
        <div class="fixed z-[1000] top-[6.5rem] xl:top-[3.5rem] h-[calc(100vh-6.5rem)] box-border xl:h-[calc(100vh-3.5rem)] w-full xl:w-3/4 xl:right-0 pb-10 lg:pb-0">
            <div id="questionContainer" class="text-gray-900 text-sm lg:text-base h-full">
                <!-- QUESTION CONTAINER BODY -->
                <div class="w-full h-[calc(100%-5rem)] box-border bg-gray-50 xl:p-4">
                    <div class="w-full h-full p-4 bg-white rounded-md shadow">
                        <div class="w-full h-full bg-primary-10 border-y rounded-md overflow-y-auto p-4 shadow-inner"> @php $i=0; @endphp
                            @foreach ($indicators as $indicator => $categories) @php $j=0; @endphp
                                @foreach ($categories as $category => $questions) @php $k=0; @endphp
                                    <!-- RESPONSE CONTAINER -->
                                    <div id="questions-container_{{$i}}_{{$j}}" class="questions-container {{ ($i==0 && $j==0) ? '' : 'hidden' }}  w-full">
                                        <!-- CATEGORY TITLE -->
                                        <p class="text-xl lg:text-2xl text-primary-70 font-extrabold tracking-wider mb-4">
                                            {{ $category }} <span class="tracking-normal font-medium text-lg lg:text-xl">(NILAI KATEGORI : <span class="category-total-score_{{$i}}_{{$j}} font-extrabold">{{ $questions->sum('score') }}</span>)</span>
                                        </p>
                                        @foreach ($questions as $question)
                                            {{-- @php
                                                dump($question);
                                            @endphp --}}
                                            <!-- MAIN CONTENT -->
                                            <div class="w-full flex gap-2 border py-3 pl-1 pr-2 rounded-md bg-white {{ $loop->index === 0 ? '' : 'mt-6' }}">
                                                <p class="text-primary-70 w-8 box-border text-sm lg:text-base text-right font-mono">{{ $k+1 }}.</p>
                                                <div id="question_main_container_{{$i}}_{{$j}}_{{$k}}" class="w-[calc(100%-2.5rem)] box-border pb-3 pr-3">
                                                    <!-- QUESTION TEXT -->
                                                    <p class="text-primary-80 tracking-tight text-sm lg:text-base font-medium p-0 mb-2">
                                                        {{ $question->text }}
                                                        <!-- QUESTION TEXT DETAIL -->
                                                        @if ($question->details)
                                                            <br>
                                                            <span class="text-xs text-primary-50 font-medium">{{ $question->details }}</span>
                                                        @endif
                                                    </p>

                                                    <!-- RADIOS FOR SCORES IF QUESTION HAS NO CHILDREN -->
                                                    @if ($question->answer === 1 || ($question->children->count() > 0 && $question->children->where('answer',1)->count() > 0))
                                                        <div>
                                                            <input type="radio" data-questionDBID="{{ $question->id }}" name="score_{{ $question->id }}" class="score-radio hidden"
                                                                id="score_{{$i}}_{{$j}}_{{$k}}--less-good"
                                                                value="{{ $question->less_good }}"
                                                                @checked($question->score ==$question->less_good)>
                                                            <input type="radio" data-questionDBID="{{ $question->id }}" name="score_{{ $question->id }}" class="score-radio hidden"
                                                                id="score_{{$i}}_{{$j}}_{{$k}}--good-enough"
                                                                value="{{ $question->good_enough }}"
                                                                @checked($question->score ==$question->good_enough)>
                                                            <input type="radio" data-questionDBID="{{ $question->id }}" name="score_{{ $question->id }}" class="score-radio hidden"
                                                                id="score_{{$i}}_{{$j}}_{{$k}}--good"
                                                                value="{{ $question->good }}"
                                                                @checked($question->score ==$question->good)>
                                                            <input type="radio" data-questionDBID="{{ $question->id }}" name="score_{{ $question->id }}" class="score-radio hidden"
                                                                id="score_{{$i}}_{{$j}}_{{$k}}--very-good"
                                                                value="{{ $question->very_good }}"
                                                                @checked($question->score ==$question->very_good)>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- RESPONSE -->
                                                    @if ($question->children->count() === 0)
                                                        <div class="w-full md:flex md:gap-3 h-fit">
                                                            @if ($question->answer === 1)
                                                                <div class="shadow shadow-primary-20 w-full md:w-[41.5%] xl:w-[31.25%] rounded-md mb-4 md:mb-0">
                                                                    <div class="flex items-center justify-center w-full gap-2 border rounded-t-md p-1">
                                                                        <p class="px-2 text-xs md:text-sm font-bold uppercase text-primary-40">Tanggapan responden </p>
                                                                        <p class="w-20 md:w-24 p-2 text-center text-xs shadow-inner shadow-emerald-700 font-black tracking-bold bg-emerald-500 text-emerald-50 rounded-md">
                                                                            YA
                                                                        </p>
                                                                    </div>
                                                                    <div class="py-2 px-3 bg-primary-10 rounded-b-md">
                                                                        <a href="{{ $question->attachment }}" target="_blank" class="flex gap-2 justify-center items-center text-primary-50 hover:text-primary">
                                                                            <p class="uppercase text-xs md:text-sm font-bold pt-0.5">Bukti Pendukung Jawaban</p>
                                                                            <span>
                                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 md:w-[1.125rem] md:h-[1.125rem]">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                                                </svg>
                                                                            </span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                                <!-- LABELS -->
                                                                <div class="flex justify-between w-full md:w-[57%] xl:w-[67.5%] rounded-md ">
                                                                    <div class="w-[calc(100%-3.5rem)] md:w-[calc(100%-4.75rem)] p-1 md:p-2 rounded-md bg-primary-10 shadow shadow-primary-20">
                                                                        <p class="text-xs md:text-sm mb-1 text-center text-primary font-bold">KESESUAIAN BUKTI PENDUKUNG</p>
                                                                        <div class="w-full grid grid-cols-4 gap-1 md:gap-1.5 xl:gap-2 p-1 md:p-1.5 xl:p-[0.4rem] rounded-md bg-gray-50 border border-primary-20">
                                                                            <label id="label_score_{{$i}}_{{$j}}_{{$k}}--less-good"
                                                                                for="score_{{$i}}_{{$j}}_{{$k}}--less-good"
                                                                                class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                                {{ $question->score == $question->less_good ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                                <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">KURANG</p>
                                                                            </label>
                                                                            <label id="label_score_{{$i}}_{{$j}}_{{$k}}--good-enough"
                                                                                for="score_{{$i}}_{{$j}}_{{$k}}--good-enough"
                                                                                class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                                {{ $question->score == $question->good_enough ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                                <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">CUKUP</p>
                                                                            </label>
                                                                            <label id="label_score_{{$i}}_{{$j}}_{{$k}}--good"
                                                                                for="score_{{$i}}_{{$j}}_{{$k}}--good"
                                                                                class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                                {{ $question->score == $question->good ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                                <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">HAMPIR</p>
                                                                            </label>
                                                                            <label id="label_score_{{$i}}_{{$j}}_{{$k}}--very-good"
                                                                                for="score_{{$i}}_{{$j}}_{{$k}}--very-good"
                                                                                class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                                {{ $question->score == $question->very_good ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                                <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">SESUAI</p>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="border border-primary rounded-md overflow-hidden w-12 md:w-16 bg-primary-10/25">
                                                                        <p class="text-xs font-bold tracking-tight p-1 bg-primary text-warning text-center">NILAI</p>
                                                                        <p id="value_score_{{$i}}_{{$j}}_{{$k}}" class="text-lg md:text-2xl text-center font-sans font-black p-1 py-2.5 md:py-[0.68rem] bg-primary-10/25 text-primary">{{ round($question->score,1) }}</p>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="shadow shadow-primary-20 w-full md:w-fit h-fit rounded-md">
                                                                    <div class="flex items-center justify-center w-full gap-1 lg:gap-2 rounded-md p-1">
                                                                        <p class="px-2 text-xs md:text-sm font-bold uppercase text-primary-40">Tanggapan responden </p>
                                                                        <p class="w-20 md:w-24 p-2 text-center text-xs shadow-inner shadow-red-700 font-black tracking-bold bg-red-500 text-red-50 rounded-md">
                                                                            TIDAK
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-3 md:mt-0 w-full md:w-[57%] xl:w-[67.5%] flex justify-center border border-primary rounded-md overflow-hidden bg-primary-10/25">
                                                                    <div class="bg-primary flex items-center w-1/2">
                                                                        <p class="text-xs font-bold tracking-tight p-1 text-warning mx-auto">NILAI</p>
                                                                    </div>
                                                                    <div class="flex items-center w-1/2">
                                                                        <p id="value_score_{{$i}}_{{$j}}_{{$k}}" class="text-lg md:text-2xl mx-auto font-sans font-black text-primary">
                                                                            {{ round($question->score,1) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else 
                                                        @php $l=0; @endphp
                                                        <div class="w-full">
                                                            @foreach ($question->children as $question_child)
                                                                <div class="w-full bg-gray-100/80 p-2 md:p-3 rounded-md mt-2 md:mt-3 border border-primary-10/25 shadow">
                                                                    <div class="w-full box-border">
                                                                        <!-- QUESTION CHILD TEXT -->
                                                                        <p class="text-primary-70 tracking-tight text-sm font-normal p-0 w-full mb-2">
                                                                            <span class="text-primary-80 font-semibold">{{ $k+1 }}.{{ $l+1 }}.</span> {{ $question_child->text }}
                                                                        </p>
                                                                        @if ($question_child->answer === 1)
                                                                            <div class="shadow shadow-primary-20 w-full md:w-fit rounded-md">
                                                                                <div class="flex items-center justify-center w-full gap-1 lg:gap-2 border rounded-t-md p-1">
                                                                                    <p class="px-1 lg:px-2 text-xs lg:text-sm font-semibold tracking-tight uppercase text-primary-40">Tanggapan responden </p>
                                                                                    <p class="w-[4.5rem] lg:w-24 p-1 lg:p-2 text-center text-xs shadow-inner shadow-emerald-700 font-medium bg-emerald-500 text-emerald-50 rounded-md">
                                                                                        YA
                                                                                    </p>
                                                                                </div>
                                                                                <div class="py-2 px-3 bg-primary-10 rounded-b-md">
                                                                                    <a href="{{ $question_child->attachment }}" target="_blank" class="flex gap-2 justify-center items-center text-primary-50 hover:text-primary">
                                                                                        <p class="uppercase text-xs lg:text-sm font-bold pt-0.5">Bukti Pendukung Jawaban</p>
                                                                                        <span>
                                                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 md:w-[1.125rem] md:h-[1.125rem]">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                                                            </svg>
                                                                                        </span>
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        @else
                                                                            <div class="shadow shadow-primary-20 w-full md:w-fit rounded-md">
                                                                                <div class="flex items-center justify-center w-full gap-1 lg:gap-2 rounded-md p-1">
                                                                                    <p class="px-1 lg:px-2 text-xs lg:text-sm font-semibold tracking-tight uppercase text-primary-40">Tanggapan responden </p>
                                                                                    <p class="w-[4.5rem] lg:w-24 p-1 lg:p-2 text-center text-xs shadow-inner shadow-red-700 font-black tracking-bold bg-red-500 text-red-50 rounded-md">
                                                                                        TIDAK
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div> @php $l++; @endphp
                                                            @endforeach
                                                        </div>
                                                        <!-- LABELS FOR QUESTION WITH CHILDREN -->
                                                        @if ($question->children->where('answer',1)->count() > 0)
                                                            <div class="flex justify-between w-full rounded-md mt-4">
                                                                <div class="w-[calc(100%-3.5rem)] md:w-[calc(100%-4.75rem)] p-1 md:p-2 rounded-md bg-primary-10 shadow shadow-primary-20">
                                                                    <p class="text-xs md:text-sm mb-1 text-center text-primary font-bold">KESESUAIAN BUKTI PENDUKUNG</p>
                                                                    <div class="w-full grid grid-cols-4 gap-1 md:gap-1.5 xl:gap-2 p-1 md:p-1.5 xl:p-[0.4rem] rounded-md bg-gray-50 border border-primary-20">
                                                                        <label id="label_score_{{$i}}_{{$j}}_{{$k}}--less-good"
                                                                            for="score_{{$i}}_{{$j}}_{{$k}}--less-good" 
                                                                            class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                            {{ $question->score == $question->less_good ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                            <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">KURANG</p>
                                                                        </label>
                                                                        <label id="label_score_{{$i}}_{{$j}}_{{$k}}--good-enough"
                                                                            for="score_{{$i}}_{{$j}}_{{$k}}--good-enough" 
                                                                            class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                            {{ $question->score == $question->good_enough ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                            <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">CUKUP</p>
                                                                        </label>
                                                                        <label id="label_score_{{$i}}_{{$j}}_{{$k}}--good"
                                                                            for="score_{{$i}}_{{$j}}_{{$k}}--good" 
                                                                            class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                            {{ $question->score == $question->good ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                            <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">HAMPIR</p>
                                                                        </label>
                                                                        <label id="label_score_{{$i}}_{{$j}}_{{$k}}--very-good"
                                                                            for="score_{{$i}}_{{$j}}_{{$k}}--very-good" 
                                                                            class="score_{{$i}}_{{$j}}_{{$k}} flex justify-center items-center cursor-pointer rounded-md hover:bg-primary-20 hover:text-primary-40
                                                                            {{ $question->score == $question->very_good ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                                                            <p class="text-[0.6rem] md:text-[0.65rem] xl:text-[0.7rem] leading-3 font-bold py-3 md:p-1.5 tracking-tighter xl:tracking-tight text-center">SESUAI</p>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="border border-primary rounded-md overflow-hidden w-12 md:w-16 bg-primary-10/25">
                                                                    <p class="text-xs font-bold tracking-tight p-1 bg-primary text-warning text-center">NILAI</p>
                                                                    <p id="value_score_{{$i}}_{{$j}}_{{$k}}" class="text-lg md:text-2xl text-center font-sans font-black p-1 py-2.5 md:py-[0.68rem] bg-primary-10/25 text-primary">
                                                                        {{ round($question->score,1) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @else 
                                                            <div class="mt-3 flex justify-center border border-primary rounded-md overflow-hidden bg-primary-10/25">
                                                                <div class="bg-primary flex items-center w-1/2">
                                                                    <p class="text-xs font-bold tracking-tight p-1 text-warning mx-auto">NILAI</p>
                                                                </div>
                                                                <div class="flex items-center w-1/2">
                                                                    <p id="value_score_{{$i}}_{{$j}}_{{$k}}" class="text-lg md:text-2xl mx-auto font-sans font-black text-primary">
                                                                        {{ round($question->score,1) }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    <!-- AUDIT : SCORE CHANGES HISTORY -->
                                                    @if ($question->audits->count() > 0)
                                                        <div class="w-full" x-data="{ showScoreHistory_{{$i}}_{{$j}}_{{$k}} : false }">
                                                            <div id="updated_by_score_{{$i}}_{{$j}}_{{$k}}" 
                                                                class="{{ $question->audits->count() > 0 && ($question->answer === 1 || ($question->children->count() > 0 && $question->children->where('answer',1)->count() > 0)) ? '' : 'hidden' }} 
                                                                    w-full mt-4 p-1.5 ">
                                                                <button x-on:click="showScoreHistory_{{$i}}_{{$j}}_{{$k}} =! showScoreHistory_{{$i}}_{{$j}}_{{$k}}" type="button" class="text-xs md:text-sm font-medium text-gray-500 flex items-center justify-start gap-2">
                                                                    <span>
                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3">
                                                                            <path fill-rule="evenodd" d="M10.53 3.47a.75.75 0 0 0-1.06 0L6.22 6.72a.75.75 0 0 0 1.06 1.06L10 5.06l2.72 2.72a.75.75 0 1 0 1.06-1.06l-3.25-3.25Zm-4.31 9.81 3.25 3.25a.75.75 0 0 0 1.06 0l3.25-3.25a.75.75 0 1 0-1.06-1.06L10 14.94l-2.72-2.72a.75.75 0 0 0-1.06 1.06Z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </span>
                                                                    <p>Riwayat perubahan nilai</p>
                                                                </button>
                                                            </div>
                                                            <div id="history_outer_container_{{$i}}_{{$j}}_{{$k}}" class="w-fit p-2 bg-primary-10/5 rounded border border-primary-20/25" x-cloak x-show="showScoreHistory_{{$i}}_{{$j}}_{{$k}}">
                                                                <table class="text-xs">
                                                                    <thead>
                                                                        <tr class="">
                                                                            <th class="py-1 px-1 md:px-3 text-center text-xs bg-primary-40 text-gray-50 uppercase border">Waktu Diubah</th>
                                                                            <th class="py-1 px-1 md:px-3 text-center text-xs bg-primary-40 text-gray-50 uppercase border">Pengubah</th>
                                                                            <th class="py-1 px-1 md:px-3 text-center text-xs bg-primary-40 text-gray-50 uppercase border">Nilai</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="history_container_{{$i}}_{{$j}}_{{$k}}">
                                                                        @foreach ($question->audits->sortByDesc('updated_at') as $history)
                                                                            <tr>
                                                                                <td class="py-1 px-1 md:px-3 border text-center text-gray-600">{{ \Carbon\Carbon::parse($history->updated_at)->isoFormat('D MMMM Y HH:mm:ss') }}</td>
                                                                                <td class="py-1 px-1 md:px-3 border text-center text-gray-600">{{ $history->user_id === Auth::user()->id ? 'Anda' : $history->name }}</td>
                                                                                <td class="py-1 px-1 md:px-3 border text-center text-gray-600">{{ $history->new_values["score"] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div> @php $k++; @endphp
                                        @endforeach
                                    </div> @php $j++; @endphp
                                @endforeach @php $i++; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                <!-- QUESTION CONTAINER FOOTER -->
                <div class="border-t bg-white w-full h-14 lg:h-20 box-border flex justify-between items-center px-3 lg:px-5">
                    <button type="button" id="" class="hidden prev-btn prev-next-btn gap-2 items-center justify-center uppercase w-40 text-white bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-md text-xs pl-2 pr-4 py-2">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Sebelumnya</span>
                    </button>
                    <div></div>
                    <button type="button" id="next--category-button_0_1" class="next-btn prev-next-btn flex gap-2 items-center justify-center uppercase w-40 text-white bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-md text-xs pl-4 pr-2 py-2">
                        <span>Berikutnya</span>
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                <path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 0 1 1.06 0l7.5 7.5a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 1 1-1.06-1.06l6.22-6.22H3a.75.75 0 0 1 0-1.5h16.19l-6.22-6.22a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    @if (Auth::user()->role !== 'JURY')
                        @if (!$submission->jury_id)
                            <button x-on:click="showSetJuryModal = true" type="button" id="submit_btn" class="hidden submit-btn gap-2 items-center justify-center uppercase w-40 text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-xs pr-5 pl-2.5 py-2.5">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                        <path d="M5.25 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM2.25 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM18.75 7.5a.75.75 0 0 0-1.5 0v2.25H15a.75.75 0 0 0 0 1.5h2.25v2.25a.75.75 0 0 0 1.5 0v-2.25H21a.75.75 0 0 0 0-1.5h-2.25V7.5Z" />
                                    </svg>
                                </span>
                                <span><span id="current-text">TENTUKAN</span> JURI</span>
                            </button>
                        @endif
                    @else
                        <button x-on:click="showEndExamPopUp = true" type="button" id="submit_btn" class="hidden submit-btn gap-2 items-center justify-center uppercase w-40 text-white bg-emerald-600 hover:bg-emerald-700 border border-emerald-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-md text-xs pr-5 pl-2.5 py-2.5">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.53 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v5.69a.75.75 0 0 0 1.5 0v-5.69l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>SIMPAN NILAI</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <!-- SET JURY MODAL -->
    <div class="fixed z-[2220] inset-0" x-cloak x-show="showSetJuryModal">
        <div class="absolute z-[2222] inset-0 bg-primary-90 bg-opacity-30 flex justify-center items-center py-4">
            <div class="bg-white w-10/12 md:w-1/2 lg:w-2/5 xl:w-1/3 rounded-md p-5 lg:p-8 flex flex-col justify-center items-center">
                <div class="w-full text-center mb-3">
                    <p class="text-lg lg:text-xl text-primary font-extrabold tracking-wide mb-4">
                        TETAPKAN JURI
                    </p>
                    <p class="font-semibold text-left text-primary-50 mb-2">
                        Anda akan menetapkan juri untuk Evaluasi Tanggapan Penilaian Unit Kerja
                    </p>
                    <div id="work_unit_name_container" class="text-primary text-left">
                        <p class="text-xs rounded-md p-1 px-2 w-full bg-primary text-warning mt-1.5 uppercase font-medium text-center">{{ $respondent->work_unit->name }}</p>
                    </div>
                </div>
                <form action="{{ route('questionnaire.setJury', $respondent->id) }}" method="POST" class="w-full">
                    @csrf @method('PUT')
                    <div class="w-full mb-4">
                        <label for="jury_id" class="w-full text-primary-50 block m-0 my-2 font-semibold text-left">Silakan pilih juri yang akan ditugaskan</label>
                        <select name="jury_id" id="jury_id" class="border-2 border-primary-20 text-primary-50 text-sm rounded-md focus:ring-primary-70 focus:border-primary-70 block w-full p-2.5" @disabled($juries->count() <= 0)>
                            <option value="" selected hidden>Pilih Juri</option>
                            @forelse ($juries as $jury)
                                <option value="{{ $jury->id }}">{{ $jury->name }}</option>
                            @empty
                                <option value="" selected hidden>Belum ada juri yang didaftarkan.</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="w-full flex justify-center items-center gap-4">
                        <button id="closeSetJuryModal" x-on:click="showSetJuryModal = false" type="button" class="block w-[49%] text-primary bg-white border border-primary focus:ring-4 focus:outline-none focus:ring-blue-200 font-medium hover:font-semibold rounded-md text-sm py-2 text-center">
                            KEMBALI
                        </button>
                        <button type="button" id="set_jury_btn" x-on:click="showSetJuryModal = false" class="block w-[49%] text-white bg-success-20 font-medium rounded-md text-sm py-2 text-center" disabled>
                            TETAPKAN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SUBMIT EXAM ANSWERS POP UP -->
    <div class="fixed z-[2220] inset-0" x-cloak x-show="showAdminEndExamPopUp">
        <div class="absolute z-[2222] inset-0 bg-primary-90 bg-opacity-30 flex justify-center items-center py-4">
            <div class="bg-white w-10/12 md:w-1/2 lg:2/5 xl:w-1/3 rounded-md p-5 lg:p-6 py-10 lg:py-12 flex flex-col justify-center items-center">
                <div class="w-full text-center mb-3">
                    <p class="text-lg lg:text-xl text-primary font-bold tracking-wide mb-2">
                        Konfirmasi Aksi Sebagai ADMIN
                    </p>
                    <p class="text-sm lg:text-base text-justify text-primary-50">
                        Saat ini terdapat juri yang ditugaskan untuk mengevaluasi penilaian ini. Apakah Anda tetap ingin melanjutkan aksi sebagai <span class="font-bold">ADMIN</span>?
                    </p>
                </div>
                <div class="w-full flex gap-2 justify-center items-center bg-warning-10 border border-warning text-warning p-1 rounded-md mb-6 xl:mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm">Aksi tidak dapat dibatalkan!</p>
                </div>
                <div class="w-full flex justify-center items-center gap-4">
                    <button type="button" x-on:click="showAdminEndExamPopUp = false" class="block w-[32%] text-primary bg-white border border-primary focus:ring-4 focus:outline-none focus:ring-blue-200 font-medium hover:font-semibold rounded-md text-sm py-2 text-center">
                        KEMBALI
                    </button>
                    <form action="{{ route('questionnaire.submitScore', $respondent->id) }}" class="block w-[32%]"  method="POST">
                        @csrf @method('PUT')
                        <div class="w-full" id="additionalFormFields"></div>
                        <button type="submit" class="block w-full text-white bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-md text-sm py-2 text-center">
                            SIMPAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SUBMIT EXAM ANSWERS POP UP -->
    <div class="fixed z-[2220] inset-0" x-cloak x-show="showEndExamPopUp">
        <div class="absolute z-[2222] inset-0 bg-primary-90 bg-opacity-30 flex justify-center items-center py-4">
            <div class="bg-white w-10/12 md:w-1/2 lg:2/5 xl:w-1/3 rounded-md p-5 lg:p-6 py-10 lg:py-12 flex flex-col justify-center items-center">
                <div class="w-full text-center mb-3">
                    <p class="text-lg lg:text-xl text-primary font-bold tracking-wide mb-2">
                        Simpan hasil evaluasi penilaian?
                    </p>
                    <p class="text-sm lg:text-base text-justify text-primary-50">
                        Setelah mengirim nilai, <span class="text-danger font-bold">anda tidak dapat mengubahnya lagi</span>, karena penilaian anda akan segera dapat dilihat oleh responden terkait.<br>
                        <span class="mt-1 font-semibold text-primary-70 text-left">
                            Catatan : Tanggapan yang 'dapat' namun tidak/belum dinilai akan mendapatkan nilai yang bersesuaian dengan kategori <span class="font-bold">CUKUP</span>.
                        </span>
                    </p>
                </div>
                <div class="w-full flex gap-2 justify-center items-center bg-warning-10 border border-warning text-warning p-1 rounded-md mb-6 xl:mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm">Aksi tidak dapat dibatalkan!</p>
                </div>
                <div class="w-full flex justify-center items-center gap-4">
                    <button type="button" x-on:click="showEndExamPopUp = false" class="block w-[32%] text-primary bg-white border border-primary focus:ring-4 focus:outline-none focus:ring-blue-200 font-medium hover:font-semibold rounded-md text-sm py-2 text-center">
                        KEMBALI
                    </button>
                    @if (Auth::user()->role === 'SUPERADMIN' || Auth::user()->role === 'ADMIN' && $submission->jury_id)
                        <button type="button" x-on:click="showEndExamPopUp = false, showAdminEndExamPopUp = true"
                            class="block w-[32%] text-white bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-md text-sm py-2 text-center">
                            SIMPAN
                        </button>
                    @else
                        <form action="{{ route('questionnaire.submitScore', $respondent->id) }}" class="block w-[32%]"  method="POST">
                            @csrf @method('PUT')
                            <div class="w-full" id="additionalFormFields"></div>
                            <button type="submit" class="block w-full text-white bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-md text-sm py-2 text-center">
                                SIMPAN
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- QUIT EXAM POP UP -->
    <div class="fixed z-[2220] inset-0" x-cloak x-show="showExitPopUp">
        <div class="absolute z-[2222] inset-0 bg-black bg-opacity-30 flex justify-center items-center py-4">
            <div class="bg-white w-10/12 md:w-1/2 lg:2/5 xl:w-1/3 rounded-md p-5 py-10 xl:py-12 flex flex-col justify-center items-center">
                <div class="w-fit text-warning mb-6 xl:mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="w-full text-center mb-4 xl:mb-6">
                    <p class="text-base xl:text-lg text-gray-900 font-semibold tracking-wide">
                        Anda yakin ingin meninggalkan halaman evaluasi penilaian?
                    </p>
                </div>
                <div class="w-full text-center mb-8 xl:mb-10">
                    <p class="text-sm xl:text-base text-gray-900">
                        Nilai saat ini akan tersimpan.
                    </p>
                </div>
                <div class="w-full flex justify-center items-center gap-2 md:gap-4">
                    <button type="button" x-on:click="showExitPopUp = false" 
                        class="block w-40 text-primary bg-white border border-primary focus:ring-4 focus:outline-none focus:ring-blue-200 font-medium hover:font-semibold rounded-md text-xs xl:text-sm py-2.5 text-cente">
                        Lanjutkan Menilai
                    </button>
                    {{-- <a href="{{ route('questionnaire.index') }}"
                        class=" block w-40 text-white bg-danger hover:bg-danger-70 border border-danger focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-md text-xs xl:text-sm py-2.5 text-center">
                        Tinggalkan Halaman
                    </a> --}}
                    <button type="button" id="leave-evaluation"
                        class=" block w-40 text-white bg-danger hover:bg-danger-70 border border-danger focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-md text-xs xl:text-sm py-2.5 text-center">
                        Tinggalkan Halaman
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/flowbite.min.js') }}"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let indicator_category_indices = [];
        let user_role;

        const ajaxCall = (url, question_id, new_score) => {
            // console.log(url);
            // console.log(question_id);
            // console.log(new_score);
            $.ajax({
                type    : "POST",
                url     : url,
                data    : {
                    _method     : 'PUT',
                    _token      : '{{ csrf_token() }}',
                    question_id : question_id,
                    score       : new_score,
                },
                dataType: 'JSON',
                beforeSend : function(){
                    $(".saved").addClass("hidden");
                    $(".saving").removeClass("hidden");
                },
                success: function (response){
                    // console.log(response);
                    let i = 0;
                    let questionnaire_all_count     = 0;
                    let questionnaire_scored_count  = 0;
                    let questionnaire_total_score   = 0;

                    $.each(response, function (indicator, categories) {
                        let indicator_all_count     = 0;
                        let indicator_scored_count  = 0;
                        let indicator_total_score   = 0;

                        let j = 0;
                        $.each(categories, function (category, questions) {
                            let category_scored_count   = 0;
                            let category_total_score    = 0;
                            indicator_all_count += questions.length;
                            $.each(questions, function (index, question) {
                                question.updated_by && category_scored_count++;
                                question.updated_by && indicator_scored_count++;
                                category_total_score += question.score;
                            });
                            indicator_total_score += category_total_score;
                            let category_percentage = Math.round((category_scored_count/questions.length)*100);

                            $(`#progres_category_container_${i}_${j}`).removeClass("category-progress-container-100");
                            $(`#progres_category_container_${i}_${j}`).removeClass("category-progress-container-66");
                            $(`#progres_category_container_${i}_${j}`).removeClass("category-progress-container-33");
                            $(`#progres_category_container_${i}_${j}`).removeClass("category-progress-container-default");

                            $(`#progres_category_${i}_${j}`).removeClass("category-progress-100");
                            $(`#progres_category_${i}_${j}`).removeClass("category-progress-66");
                            $(`#progres_category_${i}_${j}`).removeClass("category-progress-33");
                            $(`#progres_category_${i}_${j}`).removeClass("category-progress-default");

                            if ( category_percentage == 100 ) {
                                $(`#progres_category_container_${i}_${j}`).addClass("category-progress-container-100");
                                $(`#progres_category_${i}_${j}`).addClass("category-progress-100");
                            } else if ( category_percentage >= 66 ) {
                                $(`#progres_category_container_${i}_${j}`).addClass("category-progress-container-66");
                                $(`#progres_category_${i}_${j}`).addClass("category-progress-66");
                            } else if ( category_percentage >= 33 ) {
                                $(`#progres_category_container_${i}_${j}`).addClass("category-progress-container-33");
                                $(`#progres_category_${i}_${j}`).addClass("category-progress-33");
                            } else {
                                $(`#progres_category_container_${i}_${j}`).addClass("category-progress-container-default");
                                $(`#progres_category_${i}_${j}`).addClass("category-progress-default");
                            }
                            
                            $(`.category-total-score_${i}_${j}`).text(category_total_score.toFixed(1));
                            $(`#category_scored_count_${i}_${j}`).text(category_scored_count);
                            $(`#progres_category_${i}_${j}`).attr("style", `width: ${category_percentage}%`);
                            j++;
                        });
                        
                        questionnaire_all_count     += indicator_all_count;
                        questionnaire_scored_count  += indicator_scored_count;
                        questionnaire_total_score   += indicator_total_score;
                        
                        let indicator_percentage = Math.round((indicator_scored_count/indicator_all_count)*100);
                        $(`#progress_indikator_${i}`).removeClass("indicator-progess-100");
                        $(`#progress_indikator_${i}`).removeClass("indicator-progess-66");
                        $(`#progress_indikator_${i}`).removeClass("indicator-progess-33");
                        $(`#progress_indikator_${i}`).removeClass("indicator-progess-default");

                        if ( indicator_percentage == 100 ) {
                            $(`#progress_indikator_${i}`).addClass("indicator-progess-100");
                        } else if ( indicator_percentage >= 66 ) {
                            $(`#progress_indikator_${i}`).addClass("indicator-progess-66");
                        } else if ( indicator_percentage >= 33 ) {
                            $(`#progress_indikator_${i}`).addClass("indicator-progess-33");
                        } else {
                            $(`#progress_indikator_${i}`).addClass("indicator-progess-default");
                        }
                        $(`#progress_indikator_${i}`).text(`${indicator_percentage}%`);
                        $(`#indicator_total_score_${i}`).text(indicator_total_score.toFixed(1));
                        i++;
                    });

                    let questionnaire_percentage = Math.round((questionnaire_scored_count/questionnaire_all_count)*100);
                    // console.log(questionnaire_scored_count);
                    // console.log(questionnaire_all_count);
                    // console.log(questionnaire_percentage);
                    $(`#progress_questionnaire_container`).removeClass("category-progress-container-100");
                    $(`#progress_questionnaire_container`).removeClass("category-progress-container-66");
                    $(`#progress_questionnaire_container`).removeClass("category-progress-container-33");
                    $(`#progress_questionnaire_container`).removeClass("category-progress-container-default");

                    $(`#progress_questionnaire`).removeClass("category-progress-100");
                    $(`#progress_questionnaire`).removeClass("category-progress-66");
                    $(`#progress_questionnaire`).removeClass("category-progress-33");
                    $(`#progress_questionnaire`).removeClass("category-progress-default");

                    if ( questionnaire_percentage == 100 ) {
                        $(`#progress_questionnaire_container`).addClass("category-progress-container-100");
                        $(`#progress_questionnaire`).addClass("category-progress-100");
                    } else if ( questionnaire_percentage >= 66 ) {
                        $(`#progress_questionnaire_container`).addClass("category-progress-container-66");
                        $(`#progress_questionnaire`).addClass("category-progress-66");
                    } else if ( questionnaire_percentage >= 33 ) {
                        $(`#progress_questionnaire_container`).addClass("category-progress-container-33");
                        $(`#progress_questionnaire`).addClass("category-progress-33");
                    } else {
                        $(`#progress_questionnaire_container`).addClass("category-progress-container-default");
                        $(`#progress_questionnaire`).addClass("category-progress-default");
                    }

                    $("#questionnaire_scored_count").text(questionnaire_scored_count);
                    $("#questionnaire_all_count").text(questionnaire_all_count);
                    $(".questionnaire_total_score").text(questionnaire_total_score.toFixed(1));
                    $(`#progress_questionnaire`).attr("style", `width: ${questionnaire_percentage}%`);
                },
                complete: function(){
                    $(".saving").addClass("hidden");
                    $(".saved").removeClass("hidden");
                },
            });
        }

        function processQuestion(currentIndex, targetIndex){
            $(".error-msg").addClass("hidden");
            $(".text-input").removeClass("border-danger");
            $(".text-input").addClass("border-gray-300");
            
            let targetRawID         = targetIndex;
            let targetIndicatorID   = targetRawID.split('_')[1];
            let targetCategoryID    = targetRawID.split('_')[2];
            
            let currentRawID        = currentIndex;
            let currentIndicatorID  = currentRawID.split('_')[1];
            let currentCategoryID   = currentRawID.split('_')[2];

            let target_index;
            for (let i = 0; i < indicator_category_indices.length; i++) {
                if(indicator_category_indices[i] === targetRawID) {
                    target_index = i;
                    break;
                }
            }
            if(target_index == 0) {
                $("#submit_btn").removeClass("flex");
                $("#submit_btn").addClass("hidden");

                $(".prev-btn").removeClass("flex");
                $(".prev-btn").addClass("hidden");
                
                $(".next-btn").attr("id",`next--${indicator_category_indices[target_index+1]}`);
            } else if (target_index == indicator_category_indices.length-1) {
                $(".next-btn").removeClass("flex");
                $(".next-btn").addClass("hidden");

                // if(user_role === 'SUPERADMIN' || user_role === 'ADMIN') {
                //     $("#submit_btn").removeClass("hidden");
                //     $("#submit_btn").addClass("flex");
                // }

                $("#submit_btn").removeClass("hidden");
                $("#submit_btn").addClass("flex");

                $(".prev-btn").attr("id",`prev--${indicator_category_indices[target_index-1]}`);
                $(".prev-btn").removeClass("hidden");
                $(".prev-btn").addClass("flex");
            } else {
                $("#submit_btn").removeClass("flex");
                $("#submit_btn").addClass("hidden");
                
                $(".prev-btn").removeClass("hidden");
                $(".prev-btn").addClass("flex");
                
                $(".next-btn").removeClass("hidden");
                $(".next-btn").addClass("flex");

                $(".prev-btn").attr("id",`prev--${indicator_category_indices[target_index-1]}`);
                $(".next-btn").attr("id",`next--${indicator_category_indices[target_index+1]}`);
            }

            $(".category-button").removeClass("active-category-button");
            $(`#${targetIndex}`).addClass("active-category-button");
            $(".questions-container").addClass("hidden");
            $("#questions-container_"+targetIndicatorID+"_"+targetCategoryID).removeClass("hidden");
            
        }

        function formatDate() {
            const date = new Date();

            const months = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];

            const day = date.getDate();
            const month = months[date.getMonth()];
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');

            return `${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
        }


        $(document).ready(function () {
            let indicators      = @json($indicators);
            let respondent_id   = @json($respondent).id;
            user_role       = @json(Auth::user()->role);
            
            let i = 0;
            $.each(indicators, function (indicatorKey, categories) { 
                let j = 0;
                $.each(categories, function (categoryKey, value) { 
                    indicator_category_indices.push(`category-button_${i}_${j}`); j++;
                }); i++;
            });

            $(".score-radio").change(function (e) { 
                e.preventDefault();

                let rawID           = $(this).attr('id');
                let cleanID         = rawID.split('--')[0];
                let indicatorID     = rawID.split('_')[1];
                let categoryID      = rawID.split('_')[2];
                let questionID      = rawID.split('_')[3].split('--')[0];
                let value           = $(this).val();
                let questionDBID    = $(this).attr("data-questionDBID");
                let classSelector   = rawID.split('-')[0];

                // console.log("rawID           : "+rawID);
                // console.log("cleanID         : "+cleanID);
                // console.log("indicatorID     : "+indicatorID);
                // console.log("categoryID      : "+categoryID);
                // console.log("questionID      : "+questionID);
                // console.log("value           : "+value);
                // console.log("questionDBID    : "+questionDBID);
                // console.log("respondentID    : "+respondent_id);
                // console.log("classSelector   : "+classSelector);

                let history_outer_container = $(`#history_outer_container_${indicatorID}_${categoryID}_${questionID}`);

                if (history_outer_container.length > 0) {
                    let current_histories = $(`#history_container_${indicatorID}_${categoryID}_${questionID}`).html();
                    let new_history = `
                        <tr>
                            <td class="py-1 px-1 md:px-3 border text-center text-gray-600">${ formatDate() }</td>
                            <td class="py-1 px-1 md:px-3 border text-center text-gray-600">Anda</td>
                            <td class="py-1 px-1 md:px-3 border text-center text-gray-600">${value}</td>
                        </tr>
                    `;
                    $(`#history_container_${indicatorID}_${categoryID}_${questionID}`).html(new_history+current_histories);
                } else {
                    let content = `
                        <div class="w-full" x-data="{ showScoreHistory_${indicatorID}_${categoryID}_${questionID} : false }">
                            <div id="updated_by_score_${indicatorID}_${categoryID}_${questionID}" 
                                class="w-full mt-4 p-1.5 ">
                                <button x-on:click="showScoreHistory_${indicatorID}_${categoryID}_${questionID} =! showScoreHistory_${indicatorID}_${categoryID}_${questionID}" type="button" class="text-xs md:text-sm font-medium text-gray-500 flex items-center justify-start gap-2">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-3">
                                            <path fill-rule="evenodd" d="M10.53 3.47a.75.75 0 0 0-1.06 0L6.22 6.72a.75.75 0 0 0 1.06 1.06L10 5.06l2.72 2.72a.75.75 0 1 0 1.06-1.06l-3.25-3.25Zm-4.31 9.81 3.25 3.25a.75.75 0 0 0 1.06 0l3.25-3.25a.75.75 0 1 0-1.06-1.06L10 14.94l-2.72-2.72a.75.75 0 0 0-1.06 1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                    <p>Riwayat perubahan nilai</p>
                                </button>
                            </div>
                            <div id="history_outer_container_${indicatorID}_${categoryID}_${questionID}" class="w-fit p-2 bg-primary-10/5 rounded border border-primary-20/25" x-cloak x-show="showScoreHistory_${indicatorID}_${categoryID}_${questionID}">
                                <table class="text-xs">
                                    <thead>
                                        <tr class="">
                                            <th class="py-1 px-1 md:px-3 text-center text-xs bg-primary-40 text-gray-50 uppercase border">Waktu Diubah</th>
                                            <th class="py-1 px-1 md:px-3 text-center text-xs bg-primary-40 text-gray-50 uppercase border">Pengubah</th>
                                            <th class="py-1 px-1 md:px-3 text-center text-xs bg-primary-40 text-gray-50 uppercase border">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history_container_${indicatorID}_${categoryID}_${questionID}">
                                        <tr>
                                            <td class="py-1 px-1 md:px-3 border text-center text-gray-600">${ formatDate() }</td>
                                            <td class="py-1 px-1 md:px-3 border text-center text-gray-600">Anda</td>
                                            <td class="py-1 px-1 md:px-3 border text-center text-gray-600">${value}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;

                    $(`#question_main_container_${indicatorID}_${categoryID}_${questionID}`).append(content);
                }

                $(`.${classSelector}`).removeClass('bg-primary text-white');
                $(`.${classSelector}`).addClass('bg-gray-200 text-gray-500');

                $(`#label_${rawID}`).removeClass('bg-gray-200 text-gray-500');
                $(`#label_${rawID}`).addClass('bg-primary text-white');

                $(`#updated_by_${cleanID}`).removeClass('hidden');
                $(`#updated_by_name_${cleanID}`).text('Anda');
                $(`#value_${cleanID}`).text(value);

                ajaxCall("{{ route('questionnaire.updateScore', $respondent->id) }}", questionDBID, value);
            });

            // QUESTION NAVIGATION BY CATEGORIES BUTTON
            $(".category-button").click(function (e) { 
                e.preventDefault();
                let targetRawID         = $(this).attr('id');
                let currentRawID        = $(".category-button.active-category-button").attr('id');
                
                // CALL THE MAIN PROCESS
                processQuestion(currentRawID, targetRawID);
            });

            // QUESTION NAVIGATION BY NEXT & PREV BUTTON
            $("#questionContainer").on("click", ".prev-next-btn", function(e){
                e.preventDefault();

                // Getting question index in the Array
                let currentRawID;
                let targetRawID     = $(this).attr('id').split("--")[1];
                let parts           = targetRawID.split("_");
                let newParts        = parts.slice(0, -1);
                if ($(this).attr('id').split("--")[0] === 'prev') {
                    currentRawID    = newParts.join("_") + "_" + (parseInt(parts[parts.length-1])+1).toString();
                } else {
                    currentRawID    = newParts.join("_") + "_" + (parseInt(parts[parts.length-1])-1).toString();
                }
                
                // CALL THE MAIN PROCESS
                processQuestion(currentRawID, targetRawID);
            });

            $("#leave-evaluation").click(function (e) { 
                e.preventDefault();
                window.close();
            });

            $("#jury_id").change(function (e) { 
                e.preventDefault();
                if ($(this).val() != "") {
                    $("#set_jury_btn").attr("disabled", false);
                    $("#set_jury_btn").removeClass("bg-success-20");
                    $("#set_jury_btn").addClass("bg-success hover:bg-success-70 border border-success focus:ring-4 focus:outline-none focus:ring-blue-300");
                } else {
                    $("#set_jury_btn").attr("disabled", true);
                    $("#set_jury_btn").removeClass("bg-success hover:bg-success-70 border border-success focus:ring-4 focus:outline-none focus:ring-blue-300");
                    $("#set_jury_btn").addClass("bg-success-20");
                }
                console.log($(this).val());
            });

            $("#closeSetJuryModal").click(function (e) { 
                e.preventDefault();
                $("#set_jury_btn").attr("disabled", true);
                $("#set_jury_btn").removeClass("bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300");
                $("#set_jury_btn").addClass("bg-primary-20");
                $("#jury_id").val("");
            });

            $("#set_jury_btn").click(function (e) { 
                e.preventDefault();
                $("#set_jury_btn").attr("disabled", true);
                $("#set_jury_btn").removeClass("bg-primary hover:bg-primary-70 border border-primary focus:ring-4 focus:outline-none focus:ring-blue-300");
                $("#set_jury_btn").addClass("bg-primary-20");
                
                let jury_id = $("#jury_id").val();
                let respondent_id = @json($respondent->id);

                $.ajax({
                    type        : "POST",
                    url         : `{{ route('questionnaire.setJury', $respondent->id) }}`,
                    data        : {
                        _method     : 'PUT',
                        _token      : '{{ csrf_token() }}',
                        jury_id     : $("#jury_id").val()
                    },
                    dataType    : "JSON",
                    success     : function (response) {
                        console.log(response)
                        Swal.fire({
                            title               : 'Berhasil',
                            text                : 'Juri berhasil ditetapkan.',
                            icon                : 'success',
                            showConfirmButton   : false,
                            timer               : 2000
                        });
                        $('#submit_btn').remove();
                    }
                });


            });
        });
    </script>
    @livewireScripts
</body>
</html>