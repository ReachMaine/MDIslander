<?php 
/* election results short codes */
add_shortcode('electionresultstown', 'electionResults_Town');
add_shortcode('electionresultsrace', 'electionResults_Race');
add_shortcode('electionresultsracesum', 'electionResults_RaceSummary');

/********** RESULTS BY TOWN ************************/
function electionResults_Town ($atts) {
/* shortcode to return all the results for a particular town */
    global $wpdb;
    $table = "election_2014"; 
	$a = shortcode_atts( array(
        'town' => 'something',
    ), $atts );
    $town = $a['town'];

    /* initializations */
    $htmlreturn = '<div class="eai-results-wrapper"><div class="eai-town"><h4>Elections results for '.$town.".</h4>";
    $found_votes = false; 
    // GET THE RACES for the given town
    $racesquery = 'SELECT  distinct `race`, registeredvoters FROM `'.$table.'` WHERE town="'. $town.'" ORDER BY raceorder';
    //echo '<p>RacesQuery: '.$racesquery.'</p>'; // testing
    $racesresults = $wpdb->get_results($racesquery); 
    //var_dump($racesresults); // testing

    if ($racesresults) {
        foreach ($racesresults as $race) {
            $total_voters = $race->registeredvoters;
            //echo '<p> Race:'.$race->race.' Reg. voters:'.$race->registeredvoters.'</p>'; // testing
            $indracequery = 'SELECT DISTINCT candidate, party, votes, town, reported FROM '.$table.' WHERE town = "'.$town.'" AND race="'.$race->race.'"';
            //echo '<p>'.$indracequery.'</p>'; // testing
            $indraceresults = $wpdb->get_results($indracequery); 

            if ($indraceresults) {
                //$htmlreturn .= '<h4>'.$race->race.'</h4>';
                $htmlreturn .= '<table class="eai-results eai-results-town"><tr class="eai-results-headerrow"><th class="eai-results-header">'.$race->race.'</th><th class="eai-result-votes">Votes</th></tr>';
                $count_voted = 0;
                foreach ($indraceresults as $indrace) {
                    if ($indrace->party) {
                        $party_string = ' (<span class="party-'.$indrace->party.'">'.$indrace->party.'</span>) ';
                    } else {
                        $party_string = '';
                    }
                    if ($indrace->reported) {
                        $found_votes = true;
                        $htmlreturn .= '<tr><td>'.$indrace->candidate.$party_string.'</td><td class="eai-result-votes">'.number_format_i18n($indrace->votes).'</td></tr>';
                        $count_voted += $indrace->votes;
                    } else {
                        $htmlreturn .= '<tr><td>'.$indrace->candidate.$party_string.'</td><td>not yet available</td></tr>';
                    }
                }
                $htmlreturn .= '</table>';
                if ($count_voted && $total_voters) {
                    $htmlreturn .= '<p> Voter participation: '.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters).' : '.round(($count_voted/$total_voters)*100).'%</p>';
                }
               
            }
        }
    } else {
        $htmlreturn .="<p>No results</p>";
    }
    $htmlreturn .="</div></div>"; // end of shortcode 
    return $htmlreturn;
} /* end of electionresultstown */
/********** END OF RESULTS BY TOWN *****************/

/********** RESULTS BY RACE ************************/
function electionResults_Race ($atts) {
    /* short code function to display election results by Race.  Ex:  Governor's race */
    global $wpdb;
    $table = "election_2014"; 
	$a = shortcode_atts( array(
        'race' => '',
    ), $atts );

    // initializations
    $race = $a['race'];
    $count_precinct_reporting = 0;
    $count_precincts = 0;
    $count_voted = 0;
    $total_voters = 0;
    $found_votes = false;
    $jsreturn = "";

    /* get the candidates in the race */ 
    $candquery = 'SELECT  distinct `candidate`, party FROM `election_2014` WHERE race="'. $race.'"';
    $candresult = $wpdb->get_results($candquery); 
    //var_dump($candresult);

    if ($candresult) {
        $racequery = 'SELECT distinct base.precinct, reported, registeredvoters ';
        $c=0;
        $sums = array();
        foreach ($candresult as $cand) {
            $c++;
            $ctabname = (string)$c;
            $ctabname = 'c'.$ctabname;
            $candidate_name = $cand->candidate;
            $sums[$candidate_name] = 0; 
            //echo "ctabname = ". $ctabname;
            //echo "<p>".$candidate_name."</p>";
            $racequery .= ', (select votes FROM `'.$table.'` '. $ctabname.' WHERE '.$ctabname.'.race="'.$race.'" AND '.$ctabname.'.candidate = "'.$candidate_name .'" and '.$ctabname.'.precinct = base.precinct) `'.$candidate_name.'` ';
        }
        $num_candidates = $c;
        $racequery .= ' FROM `'.$table.'` base ';
        $racequery .= ' WHERE base.race="'.$race.'"';
        $racequery .= ' ORDER BY base.precinct';
        //echo "<p>".$racequery."</p>";  // for testing
        //echo "<p>----</p>";// for testing
        $raceresults = $wpdb->get_results($racequery);
        //var_dump($raceresults); // for testing

        /* loop thought calc the sums & totals */
        foreach ($raceresults as $raceresult) {
            $htmlreturn .= "<tr>";
            $htmlreturn .= "<td>".$raceresult->precinct."</td>";
            if ($raceresult->reported) {
                $found_votes = true;
                $count_precinct_reporting++;
                for ($i=0; $i< $num_candidates; $i++) {
                    $candidate_name = $candresult[$i]->candidate;
                    $race_amount = $raceresult->$candidate_name;
                    $sums[$candidate_name ] = $sums[$candidate_name] + $race_amount;
                    $count_voted += $race_amount;
                }
            }
            $total_voters += $raceresult->registeredvoters;         
            $count_precincts++;
        }

        $htmlreturn = '<div class="eai-results-wrapper"><div class="eai-race">';
        $htmlreturn .= '<h4>Elections results for '.$race.'.</h4>';
       // $found_votes = false;
        /* display the results */
        if ($raceresults) {
            // first some of the totals & counts 
        
            $htmlreturn .= '<ul class="eai-results-sum">';
            for ($i=0; $i< $num_candidates; $i++) {
                $candidate_name = $candresult[$i]->candidate;
                if ($candresult[$i]->party) {
                        //$party_string = ' ('.$candresult[$i]->party.') ';
                        $party_string = ' (<span class="party-'.$candresult[$i]->party.'">'.$candresult[$i]->party.'</span>) ';
                    } else {
                        $party_string = '';
                }
                $htmlreturn .= "<li>". $candidate_name.$party_string;
                if ($found_votes) {
                    $htmlreturn .= ' : '.number_format_i18n($sums[$candidate_name]); 
                }
                $htmlreturn .= "</li>";
            }
            $htmlreturn .= '</ul>';
            if ($found_votes) {
                $htmlreturn .= "<p>".$count_precinct_reporting.' of '.$count_precincts.' Precincts reporting: '.round(($count_precinct_reporting/$count_precincts)*100).'%</p>';
                $htmlreturn .= '<p>'.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters).' voters. Participation: '.round(($count_voted/$total_voters)*100).'%</p>';
                // now the table of all the results 
                $htmlreturn .= '<table class="eai-results eai-results-race eai-results-'.$race.' ">';
                $htmlreturn .= '<tr class="eai-results-headerrow"><th>Town</th>';
                foreach ($candresult as $cand) {
                   if ($cand->party) {
                            //$party_string = ' ('.$cand->party.') ';
                            $party_string = ' (<span class="party-'.$cand->party.'">'.$cand->party.'</span>) ';
                        } else {
                            $party_string = '';
                    } 
                   $htmlreturn .= '<th class="eai-result-votes">'.$cand->candidate.$party_string.'</th>';
                }
                $htmlreturn .= "</tr>";
                foreach ($raceresults as $raceresult) {
                    $htmlreturn .= "<tr>";
                    $htmlreturn .= "<td>".$raceresult->precinct.'</td>';

                    for ($i=0; $i< $num_candidates; $i++) {
                        $candidate_name = $candresult[$i]->candidate;
                        if ($raceresult->reported) {
                            $race_amount = $raceresult->$candidate_name; // name of column is candidates name.
                            $race_amount_str = number_format_i18n($race_amount);
                        } else {
                            $race_amount_str = 'Not yet reported.';
                        }
                        //$sums[$candidate_name ] = $sums[$candidate_name] + $race_amount;
                        $htmlreturn .= '<td class="eai-result-votes">'.$race_amount_str."</td>";
                    }
                    $htmlreturn .= "</tr>";
                }

                // put the sums at the bottom of the table 
           
                $htmlreturn .= '<tr class="eai-results-totalrow"><td>Totals</td>';
                for ($i=0; $i< $num_candidates; $i++) {
                    $candidate_name = $candresult[$i]->candidate;
                    $htmlreturn .= '<td class="eia-result-totals">'.number_format_i18n($sums[$candidate_name])."</td>"; // $sumresult->
                }
                $htmlreturn .= "</tr>";
                $htmlreturn .= "<tr><th>Town</th>";
                foreach ($candresult as $cand) {
                    if ($cand->party) {
                            $party_string = ' (<span class="party-'.$cand->party.'">'.$cand->party.'</span>) ';
                        } else {
                            $party_string = '';
                    } 
                   $htmlreturn .= '<th class="eai-result-votes">'.$cand->candidate.$party_string.'</th>';
                }
                $htmlreturn .= "</tr>";
                $htmlreturn .="</table>";
            } else {
                // no votes yet.
                $htmlreturn .= "<p>No results yet, check back soon.</p>";
            }             
        } else {
            $htmlreturn .= "<p>No results.</p>";
            //var_dump($raceresults);
            //echo $racequery;
        }

    } else {
        $htmlreturn .= "<p>No Candidates</p>";
    }
    $htmlreturn .="</div></div>"; // end of wrapper & ident div
    return $htmlreturn;
}
/********** END OF RESULTS BY RACE *****************/

/********** RACE SUMMARY **********************/
function electionResults_RaceSummary ($atts) {
    /* short code function to display Summary of election results by Race.  Ex:  Governor's race */
    global $wpdb;
    $table = "election_2014"; 
    $a = shortcode_atts( array(
        'race' => '',
        'cat' => 0,
        'link' => ''
    ), $atts );

    // initializations
    $race = $a['race'];
    $link = "";
    if ($a['cat'])  {
        //$link = site_url().'?cat='.$a['cat'];
        $catid = get_cat_ID($a['cat']);
        if ($catid) {
             $link = site_url().'/?cat='.$catid;
        }
    } else {
        $link = $a['link'];
    }

    $count_precinct_reporting = 0;
    $count_precincts = 0;
    $count_voted = 0;
    $total_voters = 0;
    $found_votes = false;

    /* get the candidates in the race */ 
    $candquery = 'SELECT  distinct `candidate`, party FROM `election_2014` WHERE race="'. $race.'"';
    $candresult = $wpdb->get_results($candquery); 
    //var_dump($candresult);

    if ($candresult) {
        $racequery = 'SELECT distinct base.precinct, reported, registeredvoters ';
        $c=0;
        $sums = array();
        foreach ($candresult as $cand) {
            $c++;
            $ctabname = (string)$c;
            $ctabname = 'c'.$ctabname;
            $candidate_name = $cand->candidate;
            $sums[$candidate_name] = 0; 
            //echo "ctabname = ". $ctabname;
            //echo "<p>".$candidate_name."</p>";
            $racequery .= ', (select votes FROM `'.$table.'` '. $ctabname.' WHERE '.$ctabname.'.race="'.$race.'" AND '.$ctabname.'.candidate = "'.$candidate_name .'" and '.$ctabname.'.precinct = base.precinct) `'.$candidate_name.'` ';
        }
        $num_candidates = $c;
        $racequery .= ' FROM `'.$table.'` base ';
        $racequery .= ' WHERE base.race="'.$race.'"';
        $racequery .= ' ORDER BY base.precinct';
        //echo "<p>".$racequery."</p>";  // for testing
        //echo "<p>----</p>";// for testing
        $raceresults = $wpdb->get_results($racequery);
        //var_dump($raceresults);

        /* loop thought calc the sums & totals */
        foreach ($raceresults as $raceresult) {
            $htmlreturn .= "<tr>";
            $htmlreturn .= "<td>".$raceresult->precinct."</td>";
            if ($raceresult->reported) {
                $count_precinct_reporting++;
                $found_votes = true;
                for ($i=0; $i< $num_candidates; $i++) {
                    $candidate_name = $candresult[$i]->candidate;
                    $race_amount = $raceresult->$candidate_name;
                    $sums[$candidate_name ] = $sums[$candidate_name] + $race_amount;
                    $count_voted += $race_amount;
                }
            }
            $total_voters += $raceresult->registeredvoters;         
            $count_precincts++;
        }
        $htmlreturn = '<div class="eai-results-wrapper"><div class="eai-racesummary"><h4>';
        $title = 'Elections results: '.$race;
        if ($link) {
            $htmlreturn .= '<a href="'.$link.'">'.$title.'</a>';
        } else {
              $htmlreturn .= $title;
        }
        $htmlreturn .= '</h4>';
       
        /* display the results */
        if ($raceresults) {
            // first some of the totals & counts 
            $htmlreturn .= '<ul class="eai-results-sum">';
            for ($i=0; $i< $num_candidates; $i++) {
                $candidate_name = $candresult[$i]->candidate;
                if ($candresult[$i]->party) {
                        $party_string = ' (<span class="party-'.$candresult[$i]->party.'">'.$candresult[$i]->party.'</span>) ';
                    } else {
                        $party_string = '';
                }
                $htmlreturn .= "<li>". $candidate_name.$party_string;
                if ($found_votes) {
                    $htmlreturn .= ': '.number_format_i18n($sums[$candidate_name]);
                }
                $htmlreturn .='</li>';  
            }
            $htmlreturn .= '</ul>';
            if ($found_votes) {
                $htmlreturn .= "<p>".$count_precinct_reporting.' of '.$count_precincts.' Precincts reporting: '.round(($count_precinct_reporting/$count_precincts)*100).'%</p>';
                $htmlreturn .= '<p>'.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters).' voters. Participation: '.round(($count_voted/$total_voters)*100).'%</p>';                
                if ($link) {
                    $htmlreturn .= '<p> Click <a href="'.$link.'"> here for more details</a>.</p>';
                }
            } else {
                 $htmlreturn .= '<p>No results yet, check back soon.</p>';
            }
           
            
           
        } else {
            $htmlreturn .= "<p>No results</p>";
        }

    } else {
        $htmlreturn .= "<p>No Candidates</p>";
    }
    $htmlreturn .="</div></div>"; // end of wrapper & identifying div.
    return $htmlreturn;
}
/********** END OF RESULTS RACE SUMMARY *****************/
?>