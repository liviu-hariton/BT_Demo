### Technical Challange PHP

##### Avem urmatorul fisier JSON ce contine articole de stiri din 2022 impreuna cu cateva informatii despre fiecare articol.
[news.json](news.json)
##### Va fi nevoie sa se creeze un microserviciu Rest API, fara autentificare, care sa importe datele din fisierul JSON si sa expuna urmatorul endpoint pentru consumatori:

* GET: `api/news/{date: dd.mm.yyyy}`
    * Ex: `localhost/api/news/24.09.2022`
    * Tip raspuns: `application/json`
    * Returneaza toate articole publicate la data respectiva (vezi campul `pub_date`)
    * Daca apar exceptii sau date invalide, ramane la latitudinea ta cum le vei aborda
    * Maparea campurilor in raspuns se va face dupa cum urmeaza:
  ```js
        {
            "title": string, // Continut camp ABSTRACT din fisierul JSON
            "short": string, // Continut camp LEAD_PARAGRAPH din fisierul JSON
            "source": string, // Continut camp SOURCE din fisierul JSON
            "category": string, // Continut camp SECTION_NAME din fisierul JSON
            "subCategory": string, // Continut camp SUBSECTION_NAME din fisierul JSON
            "author": string, // Concatenare (firstname middlename lastname) din campul BYLINE din JSON. Daca avem mai multi autori se va returna doar primul!
            "link": string // Continut camp WEB_URL din fisierul JSON
        }
  ```
    * exemplu raspuns asteptat:
  ```json 
  {
    "totalResults": 3,
    "news" : [
        {
            "title": "The demand for repurposed ...",
            "short": "Until recently, I had never realized...",
            "source": "The New York Times",
            "category": "World",
            "subCategory": "Europe",
            "author": "Tripp Mickle",
            "link": "https://www.nytimes.com/2022/06/30/business/apple-levoff-insider-trading.html"
        },
        {
            "title": "The demand for repurposed ...",
            "short": "Until recently, I had never realized...",
            "source": "The New York Times",
            "category": null,
            "subCategory": null,
            "author": "Tripp Mickle",
            "link": "https://www.nytimes.com/2022/06/30/business/apple-levoff-insider-trading.html"
        } ,
        {
            "title": "The demand for repurposed ...",
            "short": "Until recently, I had never realized...",
            "source": "The New York Times",
            "category": "Style",
            "subCategory": null,
            "author": "Tripp Mickle",
            "link": "https://www.nytimes.com/2022/06/30/business/apple-levoff-insider-trading.html"
        } 
    ]
  }
  ```

#### Se poate folosi PHP vanilla sau orice framework existent.
#### Se pot folosi module disponibile pe https://packagist.org/
#### Se poate folosi orice forma de stocare a datelor din fisierul JSON (baze de date de orice tip)
### Solutia oferita va fi evaluata urmarind:
* Coding Standards
* Naming Conventions
* Structura proiectului
* Indeplinirea cerintelor

### Extra points:
* orice imbunatatiri aduse solutiei vor oferi puncte suplimentare la evaluarea acestuia.
* nice to have: 
  * Client Frontend care sa consume datele furnizate de microserviciu