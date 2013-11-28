PHP API for Elisa Viihde
========================

This class is intented to use with Elisa Viihde, a finnish cloud based PVR service. This class utilizes the hidden API provided by Elisa. The API is not publicly documented, but can be reverse engineered via the Elisa Viihde desktop gadget. This class exposes all the methods that are available in the said gadget.

Note that you need valid credentials to the service to utilize this API.

Method reference
----------------

`__construct($username = null, $password = null)`  
Constructor accepts optional login credentials. Login can be done separately.

`bool login($username, $password)`  
Logs in the user using the given credentials.

`logout()`  
Logs out the user.

`array getChannels()`  
Returns all the available channels and their programming info.

`array getPrograms($channel)`  
Returns the programming for the next 24 hours for the given channel. See `getChannels()` for channel names.

`object getProgram($programId)`  
Gets extended info for one particular program.

`array getReadyList($folderId = null)`  
Gets all the user's folders and files already saved in the service. Use `$folderId` to navigate, `null` returns the root folder.

`array getRecordingList()`  
Returns a list of all active recordings that the user has set up either manually or via wildcards.

`array getWildcardList()`  
Returns all the active wildcard searches.

`array getTopList()`  
Gets the user's top list.

`addRecording($programId)`  
Records the given program.

`removeRecording($programId)`  
Removes the given recording. Does not remove actual files.

`addWildcard($channel, $wildcard, $folderId)`  
Adds a wildcard recording to the given channel and folder.

`removeWildcard($wildcardId)`  
Removes the given wildcard recording

`removeReady($programViewId)`  
Removes a saved recording from the service.
