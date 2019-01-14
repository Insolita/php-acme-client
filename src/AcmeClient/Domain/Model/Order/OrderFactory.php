<?php
declare(strict_types=1);

/**
 * @see https://tools.ietf.org/html/draft-ietf-acme-acme-18#section-7.1.3
 */
namespace AcmeClient\Domain\Model\Order;

use AcmeClient\Domain\Model\Account\Id as AccountId;

class OrderFactory
{
    /**
     * @param  array $values
     * @return Order
     */
    public function create(array $values): Order
    {
        return new Order(
            new Id($values['id']),
            new Expires(
                \DateTime::createFromFormat('Y-m-d\TH:i:s+', $values['expires'])
            ),
            new Status($values['status']),
            $identifiers = $this->createIdentifiers($values['identifiers']),
            $this->createAuthorizations($values['authorizations'], $identifiers),
            new Finalize($values['finalize']),
            new AccountId((int)$values['accountId'])
        );
    }

    /**
     * @param  array          $values
     * @return FinalizedOrder
     */
    public function createFinalizedOrder(array $values): FinalizedOrder
    {
        return new FinalizedOrder(
            $values['id'],
            new Certificate($values['certificate']),
            $values['serverKey'],
            $values['accountId']
        );
    }

    /**
     * @param  array $identifiers
     * @return IdentifierStack
     */
    public function createIdentifiers(array $identifiers): IdentifierStack
    {
        $stack = new IdentifierStack();

        foreach ($identifiers as $identifier) {
            $stack->append(new Identifier($identifier));
        }

        return $stack;
    }

    /**
     * @param  array               $authorizations
     * @param  IdentifierStack $identifiers
     * @return AuthorizationStack
     */
    public function createAuthorizations(
        array $authorizations,
        IdentifierStack $identifiers
    ): AuthorizationStack {
        $identifiers = $identifiers->getValues();
        $stack   = new AuthorizationStack();

        for ($i = 0, $j = count($authorizations); $i < $j; $i++) {
            $stack->append(
                new Authorization($authorizations[$i], $identifiers[$i])
            );
        }

        return $stack;
    }
}
