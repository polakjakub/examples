<?php
/**
 * Class Movie
 * Loading movies from the DB
 */
class Movie extends OutputBase
{
    const MODE_TOP_RATED = 1;
    const MODE_DETAIL = 2;
    public $keyword;
    public $name;
    public $mode;
    public $params;

    public function __construct($name, $mode, $params = array())
    {
        $this->name = $name;
        $this->mode = $mode;
        $this->params = $params;
        $this->db = db_mysql::singleton();
    }

    /**
     * This is called from a project setup. Returns the XML related to current settings.
     * @return string
     */
    public function xml()
    {
        $this->addCallback('title', 'addCDATA');
        $this->addCallback('countries', 'loadCountries');
        $this->addCallback('genres', 'loadGenres');
        $this->addCallback('cast', 'loadCast');
        $this->addCallback('link', 'URLEncode');

        $xml = '';
        switch ($this->mode) {
            case self::MODE_TOP_RATED:
                $xml .= $this->executeTopRated();
                break;
            case self::MODE_DETAIL:
                $xml .= $this->executeDetail();
                break;
        }
        return $xml;
    }

    /**
     * Action Detail
     * @return string
     */
    protected function executeDetail()
    {
        $xml = $this->queryToXML("SELECT
        id,
        id AS countries,
        id AS genres,
        id AS cast,
        csfd_id, fdb_id, imdb_id, rating, kinobox_id, title, runtime, release_date, `year`
        FROM umdb_movie WHERE id = 94792", 'movie');
        return $xml;
    }
    /**
     * Action Top Rated
     * @return string
     */
    protected function executeTopRated()
    {
        $this->addCallback('link', 'addCDATA');

        $query = "SELECT id, rating, title, link FROM `umdb_movie`, `umdb_link`
            WHERE `umdb_link`.object_type = 'movie' AND `umdb_movie`.id = `umdb_link`.object_id
            ORDER BY rating DESC LIMIT 20";
        return $this->createElement($this->name, $this->queryToXML($query, 'm'));
    }

    /**
     * Callback function for loading movie countries
     * @param $id
     */
    public function loadCountries(&$id)
    {
        $query = sprintf('SELECT umdb_country . name FROM umdb_movie_country, umdb_country
            WHERE umdb_movie_country . movie_id = %d AND umdb_movie_country . country_id = umdb_country . id',
            $id);
        $id = $this->queryToXML($query, 'i');

    }

    /**
     * Callback function for loading movie genres
     * @param $id
     */
    public function loadGenres(&$id)
    {
        $query = sprintf('SELECT umdb_genre . * FROM umdb_movie_genre, umdb_genre
            WHERE umdb_movie_genre . movie_id = %d AND umdb_movie_genre . genre_id = umdb_genre . id',
            $id);
        $id = $this->queryToXML($query, 'i');
    }

    /**
     * Callback function for loading movie cast
     * @param $id
     */
    public function loadCast(&$id)
    {
        $query = sprintf('SELECT per.id, fullname, birth, death, umdb_link.link
            FROM umdb_movie_cast AS umc, umdb_position AS pos, umdb_person AS per, umdb_link
            WHERE umc.movie_id = %d
            AND umc.position_id = pos.id
            AND umc.person_id = per.id
            AND umdb_link.object_id = per.id
            AND umdb_link.object_type =  "person"
            ORDER BY umc.order_by ASC ', $id);
        $id = $this->queryToXML($query, 'i');
    }
}