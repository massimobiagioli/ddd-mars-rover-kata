# (CQRS + ES) Mars Rover Kata

## Impostazione ambiente in locale

* Impostare il file <code>.env.local</code> con le configurazioni locali
* Bootstrap progetto: Lanciare il comando <code>make install</code>
* Avviare i container docker: Lanciare il comando <code>make up</code>
* Eseguire i test <code>make test</code>
* Fermare i container docker: Lanciare il comando <code>make down</code>

## Comandi da utilizzare (bin/console)

* Creazione di un nuovo Mars Rover: <code>app:mars-rover:create</code>
* Posizionare un MarsRover nel terreno: <code>app:mars-rover:place</code>
* Posizionare un MarsRover nel terreno: <code>app:mars-rover:send-primitive-command</code>
