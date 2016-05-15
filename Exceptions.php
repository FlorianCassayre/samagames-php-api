<?php

abstract class MinecraftFetchException extends Exception
{

}

class APIException extends MinecraftFetchException
{

}

class UnknownInputException extends MinecraftFetchException
{

}

class UnknownNameException extends MinecraftFetchException
{

}

class UnknownUUIDException extends MinecraftFetchException
{

}

class UnknownSamaGamesPlayerException extends MinecraftFetchException
{

}



abstract class SamaGamesPlayerException extends Exception
{

}

class PlayerStatisticsException extends Exception
{

}