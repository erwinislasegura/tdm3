USE tdm3;
ALTER TABLE referees ADD COLUMN certification_level VARCHAR(40) NULL AFTER license_code;
CREATE INDEX idx_referees_org_status ON referees(organization_id,status);
