const faker = require('faker');
const moment = require('moment');
const fs = require('graceful-fs');

seed();

function seed() {
  let counter = {'cur': 1}
  let occupancies = new Set()
  let rooms = []
  for (let lv = 1; lv <= 5; lv++) {
    for (let num = 1; num <= 6; num++) {
      rooms.push(Number(`${lv}0${num}`))
    }
  }

  while (counter['cur'] < 5000) {
    populate(counter, occupancies, rooms);
  }
}

function populate(counter, occupancies, rooms) {
  let canceled = false;

  let statements = [];
  //CUSTOMER & RESERVATION
  const fname =  faker.name.firstName()
  const lname = faker.name.lastName()
  const phone = faker.phone.phoneNumberFormat()
  const email = `${fname}_${lname}@gmail.com`
  const starts = [faker.date.soon(), faker.date.recent(), faker.date.past(), faker.date.past(), faker.date.past(), faker.date.past(),faker.date.past(), faker.date.future()]
  const _startDate = starts[Math.floor(Math.random() * starts.length)]
  const startDate = _startDate.toISOString().split('T')[0]
  const _duration = Math.floor(Math.random() * 5 + 1)
  const endDate = moment(_startDate).add(_duration, 'days').toISOString().split('T')[0]
  const bookedAt = moment().format('YYYY-MM-DD hh:mm:ss');
  const numGuests = Math.floor(Math.random()*5 + 1)
  let room = rooms[Math.floor(Math.random() * rooms.length)];
  const sources = ['direct', 'OTA', 'corporate', 'agency', 'direct', 'OTA'];
  const source = sources[Math.floor(Math.random() * sources.length)];
  statements.push(`INSERT INTO Customers(Fname, Lname, Phone, Email) VALUES ("${fname}", "${lname}", '${phone}', "${email}");`)
  statements.push(`INSERT INTO Reservations(ID, StartDate, EndDate, NumGuests, CustomerID, Source, BookedAt) VALUES(${counter['cur']}, '${startDate}', '${endDate}', ${numGuests}, ${counter['cur']}, '${source}', '${bookedAt});`)
  
  // OCCUPANCIES
  for (let j = 0; j < _duration; j++) {
    const date = moment(_startDate).add(j, 'days').toISOString().split('T')[0]
    while (occupancies.has([room, date])) {
      room = rooms[Math.floor(Math.random() * rooms.length)];
    }

    if (occupancies.has(`${room} ${date}`) == false) {
      occupancies.add(`${room} ${date}`)

      statements.push(`INSERT INTO Occupancies(RoomNo, Date, ResID) VALUES (${room}, '${date}', ${counter['cur']});`)
    } else {
      canceled = true;
      break;
    } 
  }

  if (!canceled) {
    fs.appendFileSync('data.txt', `#${counter['cur']}\n`, (err) => {
      if (err) return console.log(err);
    });    
    for (const s of statements) {
      fs.appendFileSync('data.txt', s+'\n', (err) => {
        if (err) return console.log(err);
      })
    }
    // console.log('done')
    counter['cur']++

  }

}
