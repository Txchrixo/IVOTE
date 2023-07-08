<?php

namespace App\Enums;

class RouteApp
{

    public static string $init = '';

    public static string $veriff = 'voter.veriff';
    public static string $veriffSubmitted = 'voter.veriffSubmitted';
    public static string $ready = 'voter.ready';
    public static string $generateKeys = 'voter.generateKeys';
    public static string $wait = 'voter.wait';
    public static string $vote = 'voter.vote';
    public static string $raDashboard = 'ra.dashboard';
    public static string $raCandidates = 'ra.candidates';
    public static string $raAddCandidate = 'ra.addCandidate';
    public static string $eaDashboard = 'ea.dashboard';
    public static string $eaElection = 'ea.election';
    public static string $eaAddElection = 'ea.addElection';
    public static string $eaBalance = 'ea.balance';
    public static string $eaFee = 'ea.fee';
    public static string $tallyHome = 'tally.home';

}
