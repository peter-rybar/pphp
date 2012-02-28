
create table document (
	id		integer primary key asc,
	meta		text,
	data		text,
	timestamp	datetime
	--timestamp	datetime	not null default CURRENT_TIMESTAMP
	--timestamp	datetime	not null default (datetime('now', 'localtime'))
);

/* for sqlite2
*/
create trigger insert_document_datetime_trigger after insert on document
	begin
		update document
			set timestamp = datetime('NOW', 'localtime')
			where rowid = new.rowid;
	end;

create trigger update_document_datetime_trigger after update on document
	begin
		update document
			set timestamp = datetime('NOW', 'localtime')
			where rowid = new.rowid;
	end;

/*
*/
insert into document (meta, data) values ('desc', 'data');
insert into document (meta, data) values ('desc', 'data');
insert into document (meta, data) values ('desc', 'data');

update document set meta = 'desc xxx' where id = 1;

select * from document;
select *, strftime('%s', timestamp) from document;
