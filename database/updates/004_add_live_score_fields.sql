USE tdm3;
ALTER TABLE matches ADD COLUMN live_stream_url VARCHAR(255) NULL AFTER live_updated_at;
ALTER TABLE matches ADD COLUMN overlay_enabled TINYINT(1) DEFAULT 0 AFTER live_stream_url;
