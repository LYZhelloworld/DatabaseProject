create table if not exists `Books` (
	`ISBN`		char(14) primary key,
	`title`		varchar(256) not null,
	`authors`	varchar(256),
	`publisher`	varchar(256),
	`year`		int,
	`copies`	int not null,
	`price`		decimal(5, 2),
	`format`	char(9) not null check(`format` = 'hardcover' or `format` = 'softcover'),
	`keywords`	varchar(64),
	`subject`	varchar(64)
);

create table if not exists `Customers` (
	`name`		varchar(64) not null,
	`loginname`	varchar(64) primary key,
	`password`	varchar(64) not null,
	`credit`	char(16),
	`address`	varchar(256),
	`phone`		varchar(32)
);

create table if not exists `Opinions` (
	`user`			int not null,
	`book`			char(14) not null,
	`score`			int not null check(`score` >= 1 and `score` <= 10),
	`feedback`		varchar(256),
	`feedback_date`	datetime not null default now(),	
	primary key (`user`, `book`),
	foreign key (`user`) references `Customers` (`loginname`) on update cascade on delete cascade,
	foreign key (`book`) references `Books` (`ISBN`) on update cascade on delete cascade
);

create table if not exists `Rate` (
	`user`	int not null,
	`book`		char(14) not null,
	`rating`	int not null check(`rating` >= 0 and `rating` <= 2), -- 0 for useless, 2 for very useful
	`rated_by`	int not null,
	check (`user` <> `rated_by`),
	primary key (`user`, `book`, `rated_by`),
	foreign key (`user`) references `Opinions` (`user`) on update cascade on delete cascade,
	foreign key (`book`) references `Opinions` (`book`) on update cascade on delete cascade,
	foreign key (`rated_by`) references `Customers` (`loginname`) on update cascade on delete cascade
);

create table if not exists `Orders` (
	`orderID`		int auto_increment primary key,
	`user`			int not null,
	`order_date`	datetime not null default now(),
	`order_status`	varchar(256),
	foreign key (`user`) references `Customers` (`loginname`) on update cascade,
	foreign key (`book`) references `Books` (`ISBN`) on update cascade
);

create table if not exists `OrderBooks` (
	`orderID`		int not null,
	`book`			char(14) not null,
	`copies`		int not null,
	primary key (`orderID`, `book`),
	foreign key (`orderID`) references `Orders` (`orderID`) on delete cascade,
	foreign key (`book`) references `Books` (`ISBN`) on update cascade
);

create table if not exists `Admin` (
	`username`		varchar(64) primary key,
	`password`		varchar(64) not null
);