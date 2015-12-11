CREATE TABLE Users(
	userId TEXT PRIMARY KEY,
	username TEXT UNIQUE,
	salt TEXT,
	password TEXT
);

CREATE TABLE Admins(
	userId TEXT PRIMARY KEY,
	canReview TEXT,
	canMakeAdmin TEXT, 
	FOREIGN KEY(userId) REFERENCES Users(userId)
);

CREATE TABLE Templates(
	templateId TEXT PRIMARY KEY,
	userId TEXT,
	filetype TEXT,
	overlayFiletype TEXT,
	positions TEXT,
	timeAdded INT,
	timeAccepted INT,
	acceptedBy TEXT,
	FOREIGN KEY(userId) REFERENCES Users(userId),
	FOREIGN KEY(acceptedBy) REFERENCES Users(userId)
);

CREATE TABLE SourceImages(
	sourceId TEXT PRIMARY KEY,
	userId TEXT,
	filetype TEXT,
	timeAdded INT,
	timeAccepted INT,
	acceptedBy TEXT,
	FOREIGN KEY(userId) REFERENCES Users(userId),
	FOREIGN KEY(acceptedBy) REFERENCES Users(userId)
);

CREATE TABLE TemplateRatings(
	userId TEXT,
	templateId TEXT,
	isPositive TEXT,
	FOREIGN KEY(userId) REFERENCES Users(userId),
	FOREIGN KEY(templateId) REFERENCES Templates(templateId),
	PRIMARY KEY(userId, templateId)
);

CREATE TABLE SourceRatings(
	userId TEXT,
	sourceId TEXT,
	isPositive TEXT,
	FOREIGN KEY(userId) REFERENCES Users(userId),
	FOREIGN KEY(sourceId) REFERENCES SourceImages(sourceId),
	PRIMARY KEY(userId, sourceId)
);