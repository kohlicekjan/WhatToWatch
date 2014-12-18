<?php

class CSFD {

    private static function load($url) {
        try {
            $response = @file_get_contents($url);
            if(!empty($response) and strpos($http_response_header[0], '200')){
                return json_decode($response);
            }else{
                return NULL;
            }
        } catch (Exception $e) {
            Message::addError('Server ČSFD je přetížený.');
            return NULL;
        }
    }

    public static function loadMovie($id) {
        $url = CSFD_MOVIE . $id;

        $csfd = self::load($url);
        if(empty($csfd)){
            return [];
        }
        
        
        $data['csfd_id'] = $csfd->id;
        $data['release'] = $csfd->year;
        $data['poster_url'] = empty($csfd->poster_url) ? NULL : $csfd->poster_url;
        $data['name_cs'] = $csfd->names->cs;
        $data['name_en'] = empty($csfd->names->en) ? NULL : $csfd->names->en;
        $data['plot'] = $csfd->plot;
        $data['rating'] = $csfd->rating;

        $runtime = explode('x', explode(' ', $csfd->runtime)[0]);
        if (count($runtime) > 1) {
            $data['runtime'] = $runtime[1];
            $data['type'] = 2;
        } else {
            $data['runtime'] = $runtime[0];
            $data['type'] = 1;
        }
        $data['genre'] = $csfd->genres;
        return $data;
    }

}
