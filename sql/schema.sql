create table if not exists Books (
	ISBN		char(14) not null unique primary key,
	title		varchar(256) not null,
	authors		varchar(256),
	publisher	varchar(256),
	year		int,
	copies		int not null,
	price		decimal(5, 2),
	format		char(9) not null check(format = 'hardcover' or format = 'softcover'),
	keywords	varchar(64),
	subject		varchar(64)
);

create table if not exists Customers (
	id			int not null auto_increment primary key
	name		varchar(64) not null,
	loginname	varchar(64) not null unique,
	password	varchar(64) not null,
	credit		char(16),
	address		varchar(256),
	phone		varchar(32)
);

create table if not exists Opinions (
	userID		int not null,
	book		char(14) not null,
	score		int not null check(score >= 1 and score <= 10),
	feedback	varchar(256),
	feedback_date	datetime not null default now(),	
	primary key (userID, book),
	foreign key (userID) references Customers(id) on update cascade on delete set null
	foreign key (book) references Books(ISBN) on update cascade on delete restrict
);

create table if not exists Rate (
	userID		int not null,
	book		char(14) not null,
	rating		int not null check(rating >= 0 and rating <= 2), -- 0 for useless, 2 for very useful
	rated_by	int not null,
	check (userID <> rated_by),
	primary key (userID, book, rated_by),
	foreign key (userID) references Customers(id) on update cascade on delete set null
	foreign key (book) references Books(ISBN) on update cascade on delete restrict
	foreign key (rated_by) references Customers(id) on update cascade on delete set null
);

create table if not exists Orders (
	orderID		int not null auto_increment primary key,
	userID		int not null,
	book		char(14) not null,
	order_date	datetime not null default now(),
	copies		int not null,
	order_status	varchar(256),
	foreign key (userID) references Customers(id) on update cascade on delete restrict
	foreign key (book) references Books(ISBN) on update cascade on delete restrict
);
