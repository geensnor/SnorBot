<?php

class weekend
{
    //Weekenden uit GitHub Repo halen
    public function getWeekenden(): array
    {
        $GithHubAPIUrl = 'https://api.github.com/repos/reithose/snorbot-extra/contents/';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, 'reithose:'.getenv('githubToken'));
        curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent: reithose');
        curl_setopt($curl, CURLOPT_URL, $GithHubAPIUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if (! curl_exec($curl)) {
            exit('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
        } else {
            $curlResult = json_decode(curl_exec($curl));
        }

        curl_close($curl);

        return json_decode(file_get_contents($curlResult[1]->download_url));
    }

    public function getWeekendByJaar(int $jaar): bool|object
    {
        $weekendArray = $this->getWeekenden();
        foreach ($weekendArray as $weekend) {
            if ($weekend->jaar == $jaar) {
                return $weekend;
            }
        }

        return false;
    }

    public function getWeekendText(string $text): string
    {
        preg_match("/\d{4}/", $text, $matches); //Vier cijfers uit de vraag vissen
        if (isset($matches[0])) {
            $weekendObject = $this->getWeekendByJaar($matches[0]);
            if ($weekendObject) {
                return 'In '.$weekendObject->jaar.' gingen we naar '.$weekendObject->plaats.': '.$weekendObject->omschrijving;
            } else {
                return 'In '.$matches[0].' gingen we geen weekend weg';
            }
        }
    }
}
