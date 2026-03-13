USE tdm3;

ALTER TABLE group_standings
    ADD COLUMN tiebreak_level INT NOT NULL DEFAULT 1,
    ADD COLUMN tiebreak_trace JSON NULL;

ALTER TABLE competition_formats
    ADD COLUMN manual_tiebreak_allowed TINYINT(1) NOT NULL DEFAULT 1,
    ADD COLUMN tiebreak_policy VARCHAR(40) NOT NULL DEFAULT 'ittf';
